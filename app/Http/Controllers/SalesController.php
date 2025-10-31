<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function dashboard()
    {
        return view('sales.dashboard');
    }

    public function konsinyasi()
    {
        return view('sales.konsinyasi');
    }
}
