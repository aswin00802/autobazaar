<?php
namespace App\Helpers;

class CustomHelper
{
    public static function formatInIndianStyle($amount)
    {
        $amount    = (int) $amount;
        $number    = (string) $amount;
        $lastThree = substr($number, -3);
        $rest      = substr($number, 0, -3);

        if ($rest != '') {
            $rest = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $rest);
            return '₹ ' . $rest . ',' . $lastThree;
        } else {
            return '₹ ' . $lastThree;
        }
    }
}
