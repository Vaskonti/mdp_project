<?php

namespace App\Http\Controllers;

use App\Events\CarExitEvent;
use App\Events\CarRegisterEvent;
use App\Exceptions\InvalidDatePeriodException;
use App\Exceptions\NoFreeSlots;
use App\Http\Requests\CarPostRequest;
use App\Models\Mongo\Car;
use App\Models\Parking;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CarsController extends Controller
{
    public function enterParking(CarPostRequest $request)
    {
        try {
            $car = new Car();
            $car->registrationPlate = $request['registrationPlate'];
            $car->brand = $request['brand'];
            $car->model = $request['model'];
            $car->color = $request['color'];
            $car->category = $request['category'];
            $car->card = $request['card'];
            $freeSlots = Parking::getFreeParkingSlots();
            if ($freeSlots <= 0 || $freeSlots - $car->getNeededSlots() < 0) {
                throw new NoFreeSlots();
            }
            $car->entered = Carbon::now()->format('d-m-Y H:i');
            $car->save();

            CarRegisterEvent::dispatch($car);
            return response([
                'message' => 'Car entered parking lot successfully!'
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

        $car = Car::where('registrationPlate', '=', $request['registrationPlate'])->first();
        if (!$car || isset($car->exitted)) {
            return response([
                'message' => 'Car not found!',
            ], 404);
        }

        $car->еxited = Carbon::now()->format('d-m-Y H:i');
        $categoryPrice = number_format(Parking::determinePrice($car), 2);
        if ($car->card) {
            $categoryPrice = number_format(Parking::priceWithDiscountCard($car->card, $categoryPrice), 2);
        }
        $car->sumPaid = $categoryPrice;
        $car->save();
        CarExitEvent::dispatch($car);
        return response([
            'message' => 'Car exited parking lot successfully. The sum you must pay is ' . $categoryPrice . ' lv.'
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
        $car = Car::where('registrationPlate', '=', $registrationPlate)->first();

        if (!$car || !$car->staying) {
            return response([
                'message' => 'The car is not registered in the parking system!'
            ], 404);
        }

        $categoryPrice = number_format(Parking::determinePrice($car), 2);
        if ($car->card) {
            $categoryPrice = number_format(Parking::priceWithDiscountCard($car->card, $categoryPrice), 2);
        }
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

        $cars = 0;
        if ($dateStart && $dateEnd) {
            $cars = Car::whereBetween('created_at', [$dateStart,$dateEnd])->count();
            return response([
                'message' => 'The number of unique cars entered the parking for the period (' . $dateStart->format('d-m-Y') . ' to ' . $dateEnd->format('d-m-Y') . ') lot is: ' . $cars
            ], 200);

        }
        if($dateStart && !$dateEnd)
        {
            $copy = new Carbon($dateStart);
            $copy->addDay();
            $cars = Car::whereBetween('created_at', [$dateStart, $copy])->count();

            return response([
                'message' => 'The number of unique cars for the date ('.$dateStart->format('d-m-Y').') is: '.$cars
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

        $carsSum = Car::where('sumPaid','<>', null)->sum('sumPaid');

        dd($carsSum);
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
