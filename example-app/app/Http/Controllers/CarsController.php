<?php

namespace App\Http\Controllers;

use App\Http\Requests\CarPostRequest;
use App\Models\Mongo\Car;
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

            $car->entered = Carbon::now()->toFormattedDateString();
            $car->save();

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
}
