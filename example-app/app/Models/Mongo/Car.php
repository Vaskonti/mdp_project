<?php

namespace App\Models\Mongo;

use App\Models\Parking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model as MongoModel;

class Car extends MongoModel
{
    use HasFactory;

    protected $collection = 'cars';
    protected $connection = 'mongodb';

    protected $fillable = ['registrationPlate', 'brand', 'model', 'color', 'entered', 'category'];

    public function getNeededSlots(): int
    {
        return match ($this->category) {
            "A", "a" => Parking::$SLOTS_FOR_A,
            "B", "b" => Parking::$SLOTS_FOR_B,
            "C", "c" => Parking::$SLOTS_FOR_C,
            default => 0,
        };
    }
}
