<?php

namespace App\Enums;

use Illuminate\Validation\Rules\Enum;
final class OrderStatus extends Enum
{
    const Published = 1000;
    const Pending = 1001;
    const Accepted= 1002;
    const BookingsClosed= 1003; 
    const Completed= 1004;
    const Cancelled= 1005;
    const Expired = 1006;
    const DriverCancelOrder = 1007;
    const AvailableSearchedRoutes = 1008; //This code is for got notification if searched route is available later.

    public function isPublished($value){
       
        return $value == self::Published;
    }
    public function isPending($value){
        return $value == self::Pending;
    }
    public function isAccepted($value){
        return $value == self::Accepted;
    }
    public function isBookingsClosed($value){
        return $value == self::BookingsClosed;
    }
    public function isCompleted($value){
        return $value == self::Completed;
    }
    public function isCancelled($value){
        return $value == self::Cancelled;
    }
    public function isExpired($value){
        return $value == self::Expired;
    }
    public function isDriverCancelOrder($value){
        return $value == self::DriverCancelOrder;
    }
    public function isAvailableSearchedRoutes($value){
        return $value == self::AvailableSearchedRoutes;
    }
 
    
}
