<?php

declare(strict_types = 1);

namespace App\Models\Mongo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

final class Vehicle extends Model
{

    use HasFactory;

    protected string $registrationPlate;
    protected string $brand;
    protected string $model;
    protected string $color;
    protected string $entered;

    protected $collection = 'cars';
    protected $connection = 'mongodb';
    protected $dates = [
        'entered',
        'exited',
    ];

    //@review not something major, but in general the db columns and in that number mongo fields should be lowercase_separated_by_underscores
    protected $fillable = [
        'registrationPlate',
        'brand',
        'model',
        'color',
        'entered',
        'category',
        'card',
        'sumPaid',
    ];

    public function newFromBuilder($attributes = [], $connection = null): Bus|Truck|Car|self
    {
        $model = match ($attributes['category']) {
            'A' => new Car($attributes),
            'B' => new Bus($attributes),
            'C' => new Truck($attributes),
            default => $this->newInstance(),
        };
        $model->exists = true;
        $model->setRawAttributes((array)$attributes, true);

        return $model;
    }

    public static function vehicleInsideParking(string $registrationPlate)
    {
        return self::where('registrationPlate','=',$registrationPlate)->whereNull('exited')->exists();
    }

    protected function getPrices(): array
    {
        return [
            'day' => 0,
            'night' => 0,
        ];
    }

}
