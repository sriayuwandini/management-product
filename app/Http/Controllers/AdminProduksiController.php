<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminProduksiController extends Controller
{
    public function dashboard()
    {
        return view('admin_produksi.dashboard');
    }

    public function produk()
    {
        return view('admin_produksi.produk');
    }

    public function laporan()
    {
        return view('admin_produksi.laporan_produksi');
    }
}
