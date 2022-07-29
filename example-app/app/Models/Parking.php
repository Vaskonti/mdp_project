<?php

namespace App\Models;

use App\Models\Mongo\Car;
use Cassandra\Time;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Parking extends Model
{
    use HasFactory;

    public static int $CAPACITY = 200;
    public static int $SLOTS_FOR_A = 1;
    public static int $SLOTS_FOR_B = 2;
    public static int $SLOTS_FOR_C = 4;

    const PRICE_DAY_A = 3;
    const PRICE_DAY_B = 6;
    const PRICE_DAY_C = 12;

    const PRICE_NIGHT_A = 2;
    const PRICE_NIGHT_B = 4;
    const PRICE_NIGHT_C = 8;

    public static function determinePrice(Car $car)
    {

        $startDate = new Carbon($car->entered);
        $timeNow = Carbon::now();
        $priceDay = 0;
        $priceNight = 0;

        switch ($car->category) {
            case "A":
            case "a":
                $priceDay = self::PRICE_DAY_A;
                $priceNight = self::PRICE_NIGHT_A;
                break;
            case "B":
            case "b":
                $priceDay = self::PRICE_DAY_B;
                $priceNight = self::PRICE_NIGHT_B;
                break;
            case "C":
            case "c":
                $priceDay = self::PRICE_DAY_C;
                $priceNight = self::PRICE_NIGHT_C;
                break;
            default:
                break;
        }
        $sum = 0;

        if ($timeNow->diffInDays($startDate) > 0) {
            $sum += self::getTaxForTheDayEntered($startDate->hour, $priceDay, $priceNight);

            $startDate->addDay()->setHours(0)->setMinutes(0);
            while ($timeNow->day != $startDate->day && $timeNow->month != $startDate->month && $timeNow->year != $startDate->year) {
                $sum += self::getTaxForTheDayEntered($startDate->hour, $priceDay, $priceNight);
                $startDate->addDay()->setHours(0)->setMinutes(0);
            }
            $sum += self::getTaxForTheDayEntered($startDate->hour,$priceDay, $priceNight, $timeNow->hour);
            return $sum;
        } else {
            return self::getTaxForTheDayEntered($startDate->hour, $priceDay, $priceNight, $timeNow->hour);
        }

    }

    public static function getFreeParkingSlots()
    {
        $cars = Car::where('staying', '=', 1)->get();
        $capacity = Parking::$CAPACITY;

        foreach ($cars as $car) {
            $capacity -= $car->getNeededSlots();
        }
        return $capacity;
    }

    public static function getTaxForTheDayEntered(int $startingHour, int $priceDay, int $priceNight, int $endingHour = 24): int
    {
        $sum = 0;

        if ($startingHour >= 8 && $startingHour < 18) {
            if ($endingHour <= 18) {
                return ($endingHour - $startingHour) * $priceDay;
            }
            $sum += (18 - $startingHour) * $priceDay;
            if ($endingHour <= 24) {
                $sum += ($endingHour - 18) * $priceNight;
                return $sum;
            }

        } else if ($startingHour >= 0 && $startingHour <= 8) {
            if ($endingHour <= 8) {
                return ($endingHour - $startingHour) * $priceNight;
            }
            $sum += (8 - $startingHour) * $priceNight;
            if ($endingHour <= 18) {
                return $sum + 10 * $priceDay;
            }
            $sum += 10 * $priceDay;
            if($endingHour <= 24)
            {
                 return $sum + ($endingHour - 18) * $priceNight;
            }
        } else {
            return  ($endingHour - $startingHour) * $priceNight;
        }
        return $sum;
    }
    public static function priceWithDiscountCard(string $discountCardType, $price)
    {
        if($discountCardType == "Silver")
        {
            return $price - $price / 10;
        }

        if($discountCardType == "Gold")
        {
            return $price - $price / 15;
        }
    }
}
