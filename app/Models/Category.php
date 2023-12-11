<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Category extends Model
{
    use HasFactory;

    public function drivers()
    {
        return $this->belongsToMany(Driver::class, 'drivers_categories');
    }

}
