<?php

declare(strict_types=1);

namespace App\Http\Controllers;

//@review there are includes here that are not used;
use App\Events\CarExitEvent;
use App\Events\CarRegisterEvent;
use App\Exceptions\InvalidDatePeriodException;
use App\Exceptions\NoFreeSlotsException;
use App\Helpers\Parking;
use App\Http\Requests\CarPostRequest;
use App\Http\Requests\ExitParkingRequest;
use App\Http\Requests\GetMoneyAmountRequest;
use App\Models\Mongo\Factories\VehicleFactory;
use App\Models\Mongo\Vehicle;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class CarsController extends Controller
{
    public function enterParking(CarPostRequest $request)
    {
        if (Vehicle::vehicleInsideParking($request['registrationPlate'])) {
            return response([
                'message' => 'Cannot register car! Car is already registered and has not left!',
            ], 422);
        }

        try {
            //@review there is a design pattern that fits perfectly and could be used here
            $car = VehicleFactory::build($request->toArray());
            $car->card = $request['card'];
            $freeSlots = Parking::getFreeParkingSlots();

            if ($freeSlots <= 0 || $freeSlots - $car->getNeededSlots() < 0) {
                throw new NoFreeSlotsException();
            }

            $car->save();

            //@review Practicing events is ok, but here there is no need for creating a car and adding its timestamp
            CarRegisterEvent::dispatch($car);

            return \response([
                'message' => 'Vehicle entered parking lot successfully!',
            ], 200);
        } catch (Throwable $exception) {
            //@review this is nice
            Log::error($exception->getMessage());

            return \response([
                'message' => $exception->getMessage(),
            ], 422);
        }
    }

    public function exitParking(ExitParkingRequest $request)
    {
        //@review if I enter 2 times with the same vehicle - here the first time will be returned and the vehicle will not be found!
        $car = Vehicle::where('registrationPlate', '=', $request['registrationPlate'])->latest()->first();

        if (!$car->exists() || isset($car->exited)) {
            return \response([
                'message' => 'Vehicle not found!',
            ], 404);
        }

        $categoryPrice = Parking::determinePrice($car);

        //@review there was no validation for the card so I simply entered "card" as value; What will happen - We'll start getting exceptions (UnknownCardTypeException)
        // you could number_format a little later so it is not repeated in the code;
        if ($car->card) {
            $categoryPrice = Parking::priceWithDiscountCard($car->card, $categoryPrice);
        }

        $car->sumPaid = number_format($categoryPrice, 2);
        $car->save();
        CarExitEvent::dispatch($car);

        return \response([
            'message' => 'Vehicle exited parking lot successfully. The sum you must pay is ' . $categoryPrice . ' lv.',
        ], 200);
    }

    public function getFreeSlots()
    {
        //@review ok
        $capacity = Parking::getFreeParkingSlots();

        return \response([
            'message' => 'Available parking slots are ' . $capacity,
        ], 200);
    }

    public function checkSum(string $registrationPlate)
    {
        if (!Vehicle::vehicleInsideParking($registrationPlate)) {
            return \response([
                'message' => 'The car is not registered in the parking system!',
            ], 404);
        }

        $car = Vehicle::where('registrationPlate', '=', $registrationPlate)->first();

        $categoryPrice = Cache::remember('current_sum' . $registrationPlate, now()->addHour(), static function () use (&$car) {
            //@review this is a little code duplication
            $categoryPrice = Parking::determinePrice($car);

            if ($car->card) {
                $categoryPrice = Parking::priceWithDiscountCard($car->card, $categoryPrice);
            }

            return \number_format($categoryPrice);
        });

        return \response([
            'message' => 'The sum is ' . $categoryPrice,
        ], 200);
    }

    /**
     * @throws \App\Exceptions\InvalidDatePeriodException
     * @throws \Exception
     */
    public function getNumberOfCarsForPeriod(GetMoneyAmountRequest $request): Response
    {
        $dates = $this->assignParams($request);
        $dateStart = $dates['dateStart'];
        $dateEnd = $dates['dateEnd'];

        if (!isset($dateEnd)) {
            $dateEnd = new Carbon($dateStart);
            $dateEnd->addDay()->setHour(0)->setMinute(0);
        }

        $carsCached = Cache::remember(
            'numberOfCars:start-' . $dateStart->format('m-d-Y') . ':end-' . $dateEnd->format('m-d-Y'),
            \now()->addDay(),
            static fn () => Vehicle::whereBetween('created_at', [$dateStart, $dateEnd])->count()
        );

        return \response([
            'message' => 'The number of unique cars entered the parking for the period (' . $dateStart->format(
                'd-m-Y'
            ) . ' to ' . $dateEnd->format(
                'd-m-Y',
            ) . ') lot is: ' . $carsCached,
        ], 200);
    }

    /** @throws \App\Exceptions\InvalidDatePeriodException */
    public function getMoneyAmountForPeriod(GetMoneyAmountRequest $request): Response
    {
        $dates = $this->assignParams($request);

        $dateStart = $dates['dateStart'];
        $dateEnd = $dates['dateEnd'];

        if (!isset($dateEnd)) {
            $dateEnd = new Carbon($dateStart);
            $dateEnd->addDay()->setHour(0)->setMinute(0);
        }

        $carsSum = Cache::remember(
            'moneyEarned:' . $dateStart->format('d-m-Y') . '-' . $dateEnd->format('d-m-Y'),
            \now()->addMonth(),
            static fn () => Vehicle::whereNotNull('sumPaid')->whereBetween(
                'exited',
                [$dateStart, $dateEnd]
            )->get()->sum(
                'sumPaid'
            )
        );

        return \response([
            'message' => 'The money earned for the period (' . $dateStart->format('d-m-Y') . ' to ' . $dateEnd->format(
                'd-m-Y'
            ) . ') are: ' . $carsSum . ' lv.',
        ], 200);
    }

    /** @throws \App\Exceptions\InvalidDatePeriodException */
    private function assignParams($request): array
    {

        $dateStart = new Carbon($request->get('dateStart'));
        $dateEnd = null !== $request->get('dateEnd')
            ? new Carbon($request->get('dateEnd'))
            : null;

        if ($dateEnd && $dateStart > $dateEnd) {
            throw new InvalidDatePeriodException();
        }

        return [
            'dateEnd' => $dateEnd,
            'dateStart' => $dateStart,
        ];
    }

}
