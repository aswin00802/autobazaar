<?php
namespace app\Enums;
use Illuminate\Validation\Rules\Enum;

final class BookingMode extends Enum{
    const InstantBooking = 100;
    const RequestBooking  = 101;

    public function isInstantBooking($value){
        return $value === self::InstantBooking;
    }
}
