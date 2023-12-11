<?php

namespace App\Helpers;

use App\Models\DiscountCard;
use App\Models\Mongo\Bus;
use App\Models\Mongo\Car;
use App\Models\Mongo\Truck;
use App\Models\Mongo\Vehicle;
use Illuminate\Support\Carbon;

class Parking
{
    public const INTERVAL_1_START = 0;
    public const INTERVAL_1_END = 8;

    public const INTERVAL_2_START = 8;
    public const INTERVAL_2_END = 18;

    public const INTERVAL_3_START = 18;
    public const INTERVAL_3_END = 24;

    public static int $CAPACITY = 200;

    public static function determinePrice(Vehicle $car): int
    {

        $startDate = new Carbon($car->entered);
        $timeNow = Carbon::now();
        $priceDay = $car->getPrices()['day'];
        $priceNight = $car->getPrices()['night'];
        $sum = 0;

        if ($timeNow->diffInDays($startDate) > 0) {
            $sum += self::getTaxForTheDayEntered($startDate->hour, $startDate->minute, $priceDay, $priceNight);

            $startDate->addDay()->setHours(0)->setMinutes(0);
            while ($timeNow->day != $startDate->day && $timeNow->month != $startDate->month && $timeNow->year != $startDate->year) {
                $sum += self::getTaxForTheDayEntered($startDate->hour, $startDate->minute, $priceDay, $priceNight);
                $startDate->addDay()->setHours(0)->setMinutes(0);
            }
            $sum += self::getTaxForTheDayEntered($startDate->hour, $startDate->minute, $priceDay, $priceNight, $timeNow->hour, $timeNow->minute);
            return $sum;
        } else {
            return self::getTaxForTheDayEntered($startDate->hour, $startDate->minute, $priceDay, $priceNight, $timeNow->hour, $timeNow->minute, );
        }

    }

    public static function getFreeParkingSlots()
    {
        $vehicles = Vehicle::whereNotNull('entered')
            ->whereNull('exited')
            ->get();
        $cars = $vehicles->where('category', '=', 'A')->count() * Car::NEEDED_SLOTS;
        $buses = $vehicles->where('category', '=', 'B')->count() * Bus::NEEDED_SLOTS;
        $trucks = $vehicles->where('category', '=', 'C')->count() * Truck::NEEDED_SLOTS;

        return Parking::$CAPACITY - $cars - $buses - $trucks;
    }

    public static function getTaxForTheDayEntered(int $startingHour, int $startingMinutes, int $priceDay, int $priceNight, int $endingHour = 24, int $endingMinutes = 0): int
    {
        //@review This one is hard to follow; try extracting to smaller functions; Those 8/18/24-s should not be hardcoded like that;
        $sum = 0;
        if ($startingHour == $endingHour && $startingMinutes < $endingMinutes) {
            if($startingHour >= self::INTERVAL_2_START && $startingHour < self::INTERVAL_2_END) {
                return $priceDay;
            }
            return $priceNight;
        }
        if ($startingHour >= self::INTERVAL_2_START && $startingHour < self::INTERVAL_2_END) {
            if ($endingHour < self::INTERVAL_2_END && $endingMinutes <= 59) {
                return ($endingHour - $startingHour) * $priceDay;
            }
            $sum += (self::INTERVAL_2_END - $startingHour) * $priceDay;
            if ($endingHour < self::INTERVAL_3_END && $endingMinutes <= 59) {
                $sum += ($endingHour - self::INTERVAL_2_END) * $priceNight;
                return $sum;
            }
        } elseif ($startingHour >= self::INTERVAL_1_START && $startingHour < self::INTERVAL_1_END) {
            if ($endingHour < self::INTERVAL_1_END && $endingMinutes <= 59) {
                return ($endingHour - $startingHour) * $priceNight;
            }
            $sum += (self::INTERVAL_1_END - $startingHour) * $priceNight;
            if ($endingHour <= self::INTERVAL_2_END) {
                // 10 is the span of the second interval
                return $sum + 10 * $priceDay;
            }
            $sum += 10 * $priceDay;
            if($endingHour < self::INTERVAL_3_END && $endingMinutes <= 59) {
                return $sum + ($endingHour - self::INTERVAL_3_START) * $priceNight;
            }
        } else {
            return  ($endingHour - $startingHour) * $priceNight;
        }
        return $sum;
    }

    public static function priceWithDiscountCard(string $discountCardType, float $price): float
    {
        $discount =  DiscountCard::where('type', $discountCardType)->first()['discount'];
        return $price - $price * $discount;
    }
}
