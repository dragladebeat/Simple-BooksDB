<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class HomeController extends Controller
{

    public function index(Request $request)
    {
        if (!$request->session()->has('auth')) {
            return redirect('/login');
        }

        return view('main.index');
    }
}
