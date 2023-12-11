<?php

declare(strict_types = 1);

namespace App\Models\Mongo;

final class Bus extends Vehicle
{

    const NEEDED_SLOTS = 2;
    const CATEGORY = "B";
    const PRICE_DAY = 6;
    const PRICE_NIGHT = 4;

    protected $collection = 'cars';

    private readonly string $category;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->registrationPlate = $attributes['registrationPlate'];
        $this->brand = $attributes['brand'];
        $this->model = $attributes['model'];
        $this->color = $attributes['color'];
        $this->category = self::CATEGORY;
    }

    public function getNeededSlots(): int
    {
        return self::NEEDED_SLOTS;
    }

    public function getPrices(): array
    {
        return [
          'day' => self::PRICE_DAY,
          'night' => self::PRICE_NIGHT,
        ];
    }

}
