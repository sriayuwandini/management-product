<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
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

    public function adminIndex()
    {
        $users = \App\Models\User::whereHas('products', function ($query) {
            $query->where('status', 'pending');
        })
        ->with(['products' => function($q) {
            $q->where('status', 'pending')->orderBy('created_at', 'desc');
        }, 'products.category'])
        ->orderBy('name', 'asc')
        ->paginate(10); 

        return view('admin_produksi.validasi_konsinyasi', compact('users'));
    }


    public function riwayatDisetujui()
    {
        $users = User::whereHas('products', function($q) {
            $q->where('status', 'approved');
        })->orderBy('name')->paginate(10);

        return view('admin_produksi.riwayat_disetujui', compact('users'));
    }

    public function riwayatDitolak()
    {
        $users = \App\Models\User::whereHas('products', function($q){
            $q->where('status', 'rejected');
        })
        ->with(['products' => function($q){
            $q->where('status', 'rejected')->orderBy('updated_at', 'desc');
        }, 'products.category'])
        ->orderBy('name')
        ->paginate(10);

        return view('admin_produksi.riwayat_ditolak', compact('users'));
    }


    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $product = Product::findOrFail($id);
        $product->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Status produk berhasil diperbarui!');
    }
}
