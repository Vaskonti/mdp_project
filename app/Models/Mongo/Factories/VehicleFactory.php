<?php

namespace App\Models\Mongo\Factories;

use App\Models\Mongo\Bus;
use App\Models\Mongo\Car;
use App\Models\Mongo\Truck;
use App\Models\Mongo\Vehicle;

class VehicleFactory
{
    public static function build(array $attributes)
    {
        return match ($attributes['category']) {
            'A' => new Car($attributes),
            'B' => new Bus($attributes),
            'C' => new Truck($attributes),
            default => new Vehicle(),
        };
    }
}
