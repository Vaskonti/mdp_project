<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public function drivers()
    {
        return $this->belongsToMany(Driver::class,'drivers_categories');
    }

    public static function isValidCategory(string $category): bool
    {
        $categories = Category::all();
        foreach ($categories as $item) {
            if($category == $item->category)
                return true;
        }
        return false;
    }
}
