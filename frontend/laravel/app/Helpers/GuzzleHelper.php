<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class GuzzleHelper
{
    public static function get()
    {
        $headers = [];
        if (!empty(session('auth'))) {
            $headers['Authorization'] = 'Bearer ' . session('auth')->access_token;
        }
        return new Client([
            'base_uri' => env('app_url', 'http://localhost'),
            'http_errors' => false,
            'headers' => $headers,
        ]);
    }
}
