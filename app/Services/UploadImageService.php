<?php
namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UploadImageService
{
    public static function uploadImage(Request $request, string $imageKey)
    {
        if ($request->hasFile($imageKey) && $request->file($imageKey)->isValid()) {
            $file = $request->file($imageKey);
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/auto_images');
            $file->move($destinationPath, $fileName);

            return 'uploads/auto_images/' . $fileName;
        }

        throw new \Exception('Invalid image file.');
    }

}