<?php

namespace App\Models\Mongo;

use App\Models\Parking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model as MongoModel;

class Vehicle extends MongoModel
{
    use HasFactory;

    protected string $registrationPlate;
    protected string $brand;
    protected string $model;
    protected string $color;
    protected string $entered;

    protected $collection = 'cars';
    protected $connection = 'mongodb';
    protected $dates = ['entered', 'exited'];
    protected $fillable = ['registrationPlate', 'brand', 'model', 'color', 'entered', 'category', 'card', 'sumPaid'];

    public function newFromBuilder($attributes = [], $connection = null)
    {
        $model = match ($attributes['category']) {
            'A' => new Car($attributes),
            'B' => new Bus($attributes),
            'C' => new Truck($attributes),
            default => $this->newInstance(),
        };

        $model->exists = true;

        $model->setRawAttributes((array) $attributes, true);

        $model->setConnection($connection ?: $this->connection);

        return $model;
    }
    protected function getPrices(): array
    {
        return [
            'day' => 0,
            'night' => 0
        ];
    }
}
