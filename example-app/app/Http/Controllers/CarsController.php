<?php

namespace App\Http\Controllers;

use App\Events\CarExitEvent;
use App\Events\CarRegisterEvent;
use App\Exceptions\InvalidCategoryException;
use App\Exceptions\InvalidDatePeriodException;
use App\Exceptions\NoFreeSlotsException;
use App\Http\Requests\CarPostRequest;
use App\Models\Category;
use App\Models\Mongo\Bus;
use App\Models\Mongo\Car;
use App\Models\Mongo\Truck;
use App\Models\Mongo\Vehicle;
use App\Models\Parking;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CarsController extends Controller
{
    public function enterParking(CarPostRequest $request)
    {
        try {
            $category = $request['category'];
            if (!Category::isValidCategory($category)) {
                throw new InvalidCategoryException();
            }
            $car = "";
            if ($category == "A" || $category == "a")
            {
                $car = new Car($request->toArray());
            }
            else if($category == "B" || $category == "b")
            {
                $car = new Bus($request->toArray());
            }
            else
            {
                $car = new Truck($request->toArray());
            }
            $car->card = $request['card'];
            $freeSlots = Parking::getFreeParkingSlots();
            if ($freeSlots <= 0 || $freeSlots - $car->getNeededSlots() < 0) {
                throw new NoFreeSlotsException();
            }
            $car->save();

            CarRegisterEvent::dispatch($car);
            return response([
                'message' => 'Vehicle entered parking lot successfully!'
            ], 200);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return response([
                'message' => $exception->getMessage()
            ], 422);
        }
    }

    public function exitParking(\Illuminate\Http\Request $request)
    {
        if (!isset($request['registrationPlate'])) {
            return response([
                'message' => 'You must provide registration plate!'
            ], 408);
        }

        $car = Vehicle::where('registrationPlate', '=', $request['registrationPlate'])->first();
        if (!$car || isset($car->exitted)) {
            return response([
                'message' => 'Vehicle not found!',
            ], 404);
        }

        $categoryPrice = number_format(Parking::determinePrice($car), 2);
        if ($car->card) {
            $categoryPrice = number_format(Parking::priceWithDiscountCard($car->card, $categoryPrice), 2);
        }
        $car->sumPaid = $categoryPrice;
        $car->save();
        CarExitEvent::dispatch($car);
        return response([
            'message' => 'Vehicle exited parking lot successfully. The sum you must pay is ' . $categoryPrice . ' lv.'
        ], 200);
    }

    public function getFreeSlots()
    {
        $capacity = Parking::getFreeParkingSlots();
        return response([
            'message' => 'Available parking slots are ' . $capacity,
        ], 200);
    }

    public function checkSum(string $registrationPlate)
    {
        $car = Vehicle::where('registrationPlate', '=', $registrationPlate)->first();

        if (!$car || $car->exited) {
            return response([
                'message' => 'The car is not registered in the parking system!'
            ], 404);
        }


        $categoryPrice = Cache::remember('current_sum'.$registrationPlate,now()->addHour(), function () use (&$car) {
            $categoryPrice = number_format(Parking::determinePrice($car), 2);
            if ($car->card) {
                $categoryPrice = number_format(Parking::priceWithDiscountCard($car->card, $categoryPrice), 2);
            }
            return $categoryPrice;
        });

        return response([
            'message' => 'The sum is ' . $categoryPrice,
        ], 200);
    }

    /**
     * @throws InvalidDatePeriodException
     * @throws \Exception
     */
    public function getNumberOfCarsForPeriod(\Illuminate\Http\Request $request)
    {
        $dateStart = "";
        $dateEnd = "";
        $this->assignParams($request,$dateStart,$dateEnd);

        if ($dateStart && $dateEnd) {
            $carsCached = Cache::remember('numberOfCars:start-'.$dateStart.':end-'.$dateEnd, now()->addDay(), function() use ($dateStart, $dateEnd){
                return Vehicle::whereBetween('created_at', [$dateStart,$dateEnd])->count();
            });
            return response([
                'message' => 'The number of unique cars entered the parking for the period (' . $dateStart->format('d-m-Y') . ' to ' . $dateEnd->format('d-m-Y') . ') lot is: ' . $carsCached
            ], 200);
        }
        if($dateStart && !$dateEnd)
        {
            $copy = new Carbon($dateStart);
            $copy->addDay();
            $carsCached = Cache::remember('numberOfCars:day-'.$dateStart, now()->addDay(), function() use ($dateStart, $copy) {
                return Vehicle::whereBetween('created_at', [$dateStart, $copy])->count();
            });

            return response([
                'message' => 'The number of unique cars for the date ('.$dateStart->format('d-m-Y').') is: '.$carsCached
            ], 200);
        }

        return response([
            'message' => 'Something went wrong! :('
        ], 500);
    }


    /**
     * @throws InvalidDatePeriodException
     */
    public function getMoneyAmountForPeriod(\Illuminate\Http\Request $request)
    {
        $dateStart = "";
        $dateEnd = "";
        $this->assignParams($request,$dateStart,$dateEnd);

        $sum = 0;
        if($dateStart && $dateEnd)
        {
            $cars = Vehicle::whereNotNull('sumPaid')->whereBetween('exited', [$dateStart,$dateEnd])->get(['sumPaid']);
            foreach ($cars as $car) {
                $sum += $car->sumPaid;
            }
            return response([
                'message' => 'The money earned for the period ('.$dateStart->format('d-m-Y').' to '.$dateEnd->format('d-m-Y').') are: '.$sum.' lv.'
            ], 200);
        }

        if($dateStart && !$dateEnd)
        {
            $dateCopy = new Carbon($dateStart);
            $dateCopy->addDay();

            $cars = Vehicle::whereNotNull('sumPaid')->whereBetween('exited', [$dateStart,$dateCopy])->get(['sumPaid']);
            foreach ($cars as $car) {
                $sum += $car->sumPaid;
            }
            return response([
                'message' => 'The money earned for  ('.$dateStart->format('d-m-Y').') are: '.$sum.' lv.'
            ], 200);
        }

        throw new InvalidDatePeriodException();

    }

    /**
     * @throws InvalidDatePeriodException
     */
    private function assignParams($request, &$dateStart, &$dateEnd)
    {
        $dateStart = $request->get('dateStart');
        $dateEnd = $request->get('dateEnd');

        if (isset($dateStart)) {
            $dateStart = new Carbon($dateStart);
        }

        if (isset($dateEnd)) {
            $dateEnd = new Carbon($dateEnd);
        }

        if ($dateStart > $dateEnd && $dateStart && $dateEnd) {
            throw new InvalidDatePeriodException;
        }
    }
}
