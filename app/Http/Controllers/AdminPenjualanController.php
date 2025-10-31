<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminPenjualanController extends Controller
{
    public function dashboard()
    {
        return view('admin_penjualan.dashboard');
    }

    public function transaksi()
    {
        return view('admin_penjualan.transaksi');
    }

    public function laporan()
    {
        return view('admin_penjualan.laporan_penjualan');
    }
}
