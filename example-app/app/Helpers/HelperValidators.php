<?php

namespace App\Helpers;

class HelperValidators
{
    public static function validateEGN(string $egn): bool
    {

        $weights = [2, 4, 8, 5, 10, 9, 7, 3, 6];

        if (strlen($egn) != 10) {
            return false;
        }

        $year = substr($egn, 0, 2);
        $month = substr($egn, 2, 2);
        $day = substr($egn, 4, 2);
        if ($month > 40) {
            if ( !checkdate($month-40, $day, (int)$year+2000) ) {
                return false;
            }
        } else {
            if ($month > 20) {
                if ( !checkdate($month-20, $day, (int)$year+1800) ) {
                    return false;
                }
            } else {
                if ( !checkdate($month, $day, (int)$year+1900) ) {
                    return false;
                }
            }
        }

        $checksum = substr($egn,9,1);

        $egnSum = 0;
        for ( $i=0;$i<9;$i++ ) {
            $egnSum += substr($egn,$i,1) * $weights[$i];
        }
        $valid_checksum = $egnSum % 11;
        if ( $valid_checksum == 10 ) {
            $valid_checksum = 0;
        }
        if ( $checksum == $valid_checksum ) {
            return true;
        }
        return false;
    }

}
