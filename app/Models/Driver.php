<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Driver extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'surname', 'email', 'egn', 'image', 'description'];
    protected $connection = 'mysql';

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'drivers_categories');
    }

}
