<?php
namespace App\Services;

use Illuminate\Http\JsonResponse;

class ResponseService
{
    public static function checkUser(int $statusCode = 200){
        return response()->json([
            'status'=> $statusCode,
            'message'=>'User Not Found, Please Register'
        ], $statusCode);
    }

    public static function success($data = [], string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'status' => $statusCode,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Return a validation error response.
     */
    public static function validationError(string $defaultMessage, array $errors = [], int $statusCode = 422): JsonResponse
    {
       
        $firstErrorMessage = collect($errors)->flatten()->first();
       
        return response()->json([
            'success' => false,
            'status' => $statusCode,
            'message' => $firstErrorMessage ?? $defaultMessage, // Use the first error or default message
            'errors' => $firstErrorMessage ?? $defaultMessage, // Use the same message for `errors`
        ], $statusCode);
    }
    

    /**
     * Return a generic error response.
     */
    public static function error(string $message, array $errors = [], int $statusCode = 404): JsonResponse
    {
        return response()->json([
            'success' => false,
            'status' => $statusCode,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }
}
