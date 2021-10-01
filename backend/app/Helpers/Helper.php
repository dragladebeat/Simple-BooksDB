<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Intervention\Image\Facades\Image;

class Helper
{
    public static function respondWithError($code, $message)
    {
        return response()->json([
            'error' => [
                'code' => $code,
                'message' => $message,
            ]
        ], $code);
    }

    public static function storeImage($directory, $file_name, $image_base64)
    {
        $image = Image::make($image_base64);
        $base_path = app()->basePath() . '/public/';
        $upload_path = 'uploads/';

        $file_path = $base_path . $upload_path . $directory . '/' . $file_name;
        if (!file_exists($upload_path . $directory)) {
            mkdir($upload_path . $directory, 0777, true);
        }
        $image->save($file_path);
        Log::debug(env('APP_URL') . (str_ends_with(env('APP_URL'), '/') ? '' : '/') . $upload_path . $directory . '/' . $file_name);
        return env('APP_URL') . (str_ends_with(env('APP_URL'), '/') ? '' : '/') . $upload_path . $directory . '/' . $file_name;
    }
}
