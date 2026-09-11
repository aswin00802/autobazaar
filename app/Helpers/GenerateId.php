<?php 
namespace App\Helpers;

Class GenerateId {

    public static function generateId($model, $trow, $length = 4, $prefix)
    {
        // Get the current date in the format YYYYMMDD
        $datePrefix = date('Ymd');
        
        // Find the latest ID with the current date prefix and non-null unique ID
        $latestId = $model::whereNotNull($trow) // Skip null unique IDs
            // ->where($trow, 'like', $prefix . $datePrefix . '%')
            ->orderBy('id', 'desc')
            ->value($trow);
            
        // Determine the next number to append
        if (!$latestId) {
            // Start with 1000 if no previous ID exists
            $last_number = 1000;
        } else {
            // Extract the number part after the prefix and date prefix
             $code = substr($latestId, strlen($prefix));            
            $randomLetters = substr($code, 0, 4); // Extract the first 4 characters (random letters)
             $actualLastNumber = intval(substr($code, 4)); // Extract the remaining part as the number             
            $last_number = $actualLastNumber + 1;
        }
        
        // Generate a random 4-character alphanumeric string
        $uniquePart = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'), 0, 4);
        
        // Format the number to be zero-padded to the specified length
        $formatted_number = str_pad($last_number, $length, '0', STR_PAD_LEFT);       
     
        // Combine the prefix, unique part, and formatted number
        return $prefix . strtoupper($uniquePart) . $formatted_number;
    }
    
    // public static function formatInIndianStyle($amount)
    // {
    //     $amount = (int)$amount;
    //     $number = (string)$amount;
    //     $lastThree = substr($number, -3);
    //     $rest = substr($number, 0, -3);
    
    //     if ($rest != '') {
    //         $rest = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $rest);
    //         return '₹' . $rest . ',' . $lastThree;
    //     } else {
    //         return '₹' . $lastThree;
    //     }
    // }
    
    
}