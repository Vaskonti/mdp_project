<?php

namespace App\Models\Mongo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model as MongoModel;

class Car extends MongoModel
{
    use HasFactory;

    protected $collection = 'cars';
    protected $connection = 'mongodb';

    protected $fillable = ['registrationPlate', 'brand', 'model', 'color', 'entered'];
}
