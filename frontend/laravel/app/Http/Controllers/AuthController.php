<?php

namespace App\Http\Controllers;

use App\Helpers\GuzzleHelper;
use Exception;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class AuthController extends Controller
{
    public function index(Request $request)
    {
        if ($request->session()->has('auth')) {
            return redirect('/');
        }
        return view('login');
    }

    public function login(Request $request)
    {
        $client = GuzzleHelper::get();

        try {
            $response = $client->request('POST', env('API_URL') . 'auth/login', [
                'json' => [
                    'email' => $request->input('email'),
                    'password' => $request->input('password')
                ]
            ]);

            $response_body = json_decode($response->getBody());
            switch ($response->getStatusCode()) {
                case 200:
                    session(['auth' => $response_body->auth, 'user' => $response_body->user]);
                    return redirect('/');
                    break;
                default:
                    return redirect('/login')->withError($response->getStatusCode() . ': ' . $response_body->error->message);
            }
        } catch (Exception $e) {
            return redirect('/login')->withError($e->getMessage());
        }

        return redirect('/login');
    }

    public function logout(Request $request)
    {
        $client = GuzzleHelper::get();

        try {
            $response = $client->request('POST', env('API_URL') . 'auth/logout');

            switch ($response->getStatusCode()) {
                case 200:
                    $request->session()->flush();
                    return redirect('/login')->withError('Logout successful');
                    break;
                default:
                    return redirect('/')->withError($response->getStatusCode() . ': ' . $response->getReasonPhrase());
            }
        } catch (Exception $e) {
            return redirect('/')->withError($e->getMessage());
        }

        return redirect('/');
    }
}
