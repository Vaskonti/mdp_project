<?php

namespace App\Http\Controllers;

//@review there are includes here that are not used;
// InvalidCategoryException will throw errors as it no longer exists; When deleting/renaming files use the refactor functionality of the IDE to avoid such leftovers
use App\Events\CarExitEvent;
use App\Events\CarRegisterEvent;
use App\Exceptions\InvalidCategoryException;
use App\Exceptions\InvalidDatePeriodException;
use App\Exceptions\NoFreeSlotsException;
use App\Helpers\Parking;
use App\Http\Requests\CarPostRequest;
use App\Models\Category;
use App\Models\Mongo\Bus;
use App\Models\Mongo\Car;
use App\Models\Mongo\Truck;
use App\Models\Mongo\Vehicle;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

class CarsController extends Controller
{

    public function test() {
//        $res = Vehicle::whereNotNull('entered')
//            ->whereNull('exited')
//            ->sum('sumPaid');
//        dd($res);
//        $car = Vehicle::where('registrationPlate', '=', "A11")->first();
//        dd($car);
    }

    public function enterParking(CarPostRequest $request)
    {
        //@review this could have been part of the vehicle model; also you don't need the first vehicle; you should be using ->exists()
        $carInDB = Vehicle::where('registrationPlate','=',$request['registrationPlate'])->first();
        if ($carInDB && !$carInDB->exited)
        {
            //@review it is ok
            return response([
                'message' => 'Cannot register car! Car is already registered and has not left!',
            ], 422);
        }
        try {
            //@review there is a design pattern that fits perfectly and could be used here
            $category = $request['category'];
            $car = "";
            // @review code formatting is questionable (braces should start at the end of the line)
            if ($category == "A")
            {
                $car = new Car($request->toArray());
            }
            else if($category == "B") {
                $car = new Bus($request->toArray());
            } else {
                $car = new Truck($request->toArray());
            }
            $car->card = $request['card'];
            $freeSlots = Parking::getFreeParkingSlots();
            if ($freeSlots <= 0 || $freeSlots - $car->getNeededSlots() < 0) {
                throw new NoFreeSlotsException();
            }
            $car->save();

            //@review Practicing events is ok, but here there is no need for creating a car and adding its timestamp
            CarRegisterEvent::dispatch($car);
            return response([
                'message' => 'Vehicle entered parking lot successfully!'
            ], 200);
        } catch (\Exception $exception) {
            //@review this is nice
            Log::error($exception->getMessage());

            return response([
                'message' => $exception->getMessage()
            ], 422);
        }
    }

    public function exitParking(\Illuminate\Http\Request $request)
    {
        //@review this could have been validated in a request class; You should try to better separate logic
        if (!isset($request['registrationPlate'])) {
            return response([
                'message' => 'You must provide registration plate!'
            ], 408);
        }

//@review if I enter 2 times with the same vehicle - here the first time will be retunred and the vehicle will not be found!
        $car = Vehicle::where('registrationPlate', '=', $request['registrationPlate'])->first();
        if (!$car->exists() || isset($car->exited)) {
            return response([
                'message' => 'Vehicle not found!',
            ], 404);
        }

        $categoryPrice = number_format(Parking::determinePrice($car), 2);
        //@review there was no validation for the card so I simply entered "card" as value; What will happen - We'll start getting exceptions (UnknownCardTypeException)
        // you could number_format a little later so it is not repeated in the code;
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
        //@review ok
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
            //@review this is a little code duplication
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
            //@review the IDE is trying to help you here; the dates are defined above as strings and then transformed outside of this function to carbon objects;
            // It would be nice to also format the dates that are part of the chache key
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
            //@review
            $cars = Vehicle::whereNotNull('sumPaid')->whereBetween('exited', [$dateStart,$dateEnd])->get(['sumPaid']);
            // maybe this should be somewhere else;
            //Why do you loop the cars? This is not how an aggregation should be done.
            foreach ($cars as $car) {
                $sum += $car->sumPaid;
            }
            return response([
                'message' => 'The money earned for the period ('.$dateStart->format('d-m-Y').' to '.$dateEnd->format('d-m-Y').') are: '.$sum.' lv.'
            ], 200);
        }

        if($dateStart && !$dateEnd)
        {
            //@review code dupllication
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
        //@review I guess this was extracted here to avoid code repetition;
        //A little over engineered. First this could have been done in a request class where the validation is preferred to be
        // you have the parameters in $request why pass empty strings &$dateStart, &$dateEnd? just return an array with the two dates
        //
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
