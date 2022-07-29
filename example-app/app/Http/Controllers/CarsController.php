<?php

namespace App\Http\Controllers;

use App\Events\CarExitEvent;
use App\Events\CarRegisterEvent;
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
            $freeSlots = Parking::getFreeParkingSlots();
            if($freeSlots <= 0 || $freeSlots - $car->getNeededSlots() < 0)
            {
                throw new NoFreeSlots();
            }
            $car->entered = Carbon::now()->format('d-m-Y H:i');
            $car->save();

            CarRegisterEvent::dispatch($car);
            return response([
                'message' => 'Car entered parking lot successfully!'
            ], 200);
        } catch (\Exception $exception)
        {
            Log::error($exception->getMessage());

            return response([
                'message' => $exception->getMessage()
            ], 422);
        }
    }

    public function exitParking(\Illuminate\Http\Request $request)
    {
        if(!isset($request['registrationPlate']))
        {
            return response([
                'message' => 'You must provide registration plate!'
            ], 408);
        }

        $car = Car::where('registrationPlate','=', $request['registrationPlate'])->first();
        if(!$car || $car->staying == 0)
        {
            return response([
                'message' => 'Car not found!',
            ], 404);
        }

        $car->staying = 0;

        $car->save();
        CarExitEvent::dispatch($car);
        return response([
            'message' => 'Car exited parking lot successfully'
        ], 200);
    }

    public function getFreeSlots()
    {
        $capacity = Parking::getFreeParkingSlots();
        return response([
            'message' => 'Available parking slots are '.$capacity,
        ], 200);
    }

    public function checkSum(string $registrationPlate)
    {
        $car = Car::where('registrationPlate', '=', $registrationPlate)->first();

        if(!$car || !$car->staying)
        {
            return response([
                'message' => 'The car is not registered in the parking system!'
            ], 404);
        }

        $categoryPrice = Parking::determinePrice($car);


    }
}
