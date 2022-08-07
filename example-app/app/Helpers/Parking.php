<?php

namespace App\Helpers;

use App\Exceptions\UnknownCardTypeException;
use App\Models\Mongo\Vehicle;
use Illuminate\Support\Carbon;

class Parking
{

    public static int $CAPACITY = 200;

    public static function determinePrice(Vehicle $car)
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
            $sum += self::getTaxForTheDayEntered($startDate->hour,$startDate->minute, $priceDay, $priceNight, $timeNow->hour, $timeNow->minute);
            return $sum;
        } else {
            return self::getTaxForTheDayEntered($startDate->hour,$startDate->minute, $priceDay, $priceNight, $timeNow->hour, $timeNow->minute, );
        }

    }

    public static function getFreeParkingSlots()
    {
        $cars = Vehicle::whereNotNull('entered')->whereNull('exited')->get();
        $capacity = Parking::$CAPACITY;

        foreach ($cars as $car) {
            $capacity -= $car->getNeededSlots();
        }
        return $capacity;
    }

    public static function getTaxForTheDayEntered(int $startingHour, int $startingMinutes, int $priceDay, int $priceNight, int $endingHour = 24, int $endingMinutes = 0): int
    {
        $sum = 0;
        if ( $startingHour == $endingHour && $startingMinutes < $endingMinutes)
        {
            if( $startingHour >= 8 && $startingHour < 18)
            {
                return $priceDay;
            }
            if ( $startingHour >= 18 && $startingHour < 24)
            {
                return $priceNight;
            }
            if($startingHour >= 0 && $startingHour < 8)
            {
                return $priceNight;
            }
        }

        if ($startingHour >= 8 && $startingHour < 18) {
            if ($endingHour < 18 && $endingMinutes <= 59) {
                return ($endingHour - $startingHour) * $priceDay;
            }
            $sum += (18 - $startingHour) * $priceDay;
            if ($endingHour < 24 && $endingMinutes <= 59) {
                $sum += ($endingHour - 18) * $priceNight;
                return $sum;
            }

        } else if ($startingHour >= 0 && $startingHour < 8) {
            if ($endingHour < 8 && $endingMinutes <= 59) {
                return ($endingHour - $startingHour) * $priceNight;
            }
            $sum += (8 - $startingHour) * $priceNight;
            if ($endingHour <= 18) {
                return $sum + 10 * $priceDay;
            }
            $sum += 10 * $priceDay;
            if($endingHour < 24 && $endingMinutes <= 59)
            {
                 return $sum + ($endingHour - 18) * $priceNight;
            }
        } else {
            return  ($endingHour - $startingHour) * $priceNight;
        }
        return $sum;
    }

    /**
     * @throws UnknownCardTypeException
     */
    public static function priceWithDiscountCard(string $discountCardType, float $price): float
    {
        if($discountCardType == "Silver")
        {
            return $price - $price / 10;
        }

        if($discountCardType == "Gold")
        {
            return $price - ($price * 100 / (100/15))/100;
        }
        if($discountCardType == "Platinum")
        {
            return $price - ($price * 100 / 5) /100;
        }
        throw new UnknownCardTypeException;
    }
}
