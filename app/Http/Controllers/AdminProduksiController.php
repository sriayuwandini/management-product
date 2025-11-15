<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\DaftarProduk;
use App\Models\User;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $baruUsers = User::whereHas('products', function ($q) {
            $q->where('status', 'pending')
              ->where('created_at', '>=', now()->subDays(3));
        })
        ->with(['products' => function ($q) {
            $q->where('status', 'pending')
              ->where('created_at', '>=', now()->subDays(3))
              ->orderBy('created_at', 'desc')
              ->with('daftarProduk.category');
        }])
        ->orderBy('name', 'asc')
        ->paginate(5, ['*'], 'baru_page');

        $lamaUsers = User::whereHas('products', function ($q) {
            $q->where('status', 'pending')
              ->where('created_at', '<', now()->subDays(3));
        })
        ->with(['products' => function ($q) {
            $q->where('status', 'pending')
              ->where('created_at', '<', now()->subDays(3))
              ->orderBy('created_at', 'desc')
              ->with('daftarProduk.category');
        }])
        ->orderBy('name', 'asc')
        ->paginate(5, ['*'], 'lama_page');

        return view('admin_produksi.validasi_konsinyasi', compact('baruUsers', 'lamaUsers'));
    }

    public function riwayatDisetujui()
    {
        $users = User::whereHas('products', function ($q) {
            $q->where('status', 'approved');
        })
        ->with(['products' => function($q) {
            $q->where('status', 'approved')
              ->orderBy('updated_at', 'desc')
              ->with('daftarProduk.category');
        }])
        ->orderBy('name')
        ->paginate(10);

        return view('admin_produksi.riwayat_disetujui', compact('users'));
    }

    public function riwayatDitolak()
    {
        $users = User::whereHas('products', function ($q) {
            $q->where('status', 'rejected');
        })
        ->with(['products' => function($q) {
            $q->where('status', 'rejected')
              ->orderBy('updated_at', 'desc')
              ->with('daftarProduk.category');
        }])
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

        if ($request->status === 'approved') {
            $existingProduct = Product::where('user_id', $product->user_id)
                ->where('daftar_produks_id', $product->daftar_produks_id)
                ->where('status', 'approved')
                ->first();

            if ($existingProduct) {
                $existingProduct->update([
                    'stock' => $existingProduct->stock + $product->stock,
                ]);

                StockLog::create([
                    'product_id' => $existingProduct->id,
                    'user_id' => $product->user_id,
                    'quantity' => $product->stock,
                    'type' => 'addition',
                    'description' => 'Penambahan stok dari pengajuan baru',
                ]);

                $product->delete();
            } else {
                $product->update(['status' => 'approved']);

                StockLog::create([
                    'product_id' => $product->id,
                    'user_id' => $product->user_id,
                    'quantity' => $product->stock,
                    'type' => 'addition',
                    'description' => 'Persetujuan awal produk konsinyasi',
                ]);
            }

            return back()->with('success', '✅ Produk disetujui dan stok berhasil diperbarui!');
        }

        $product->update(['status' => 'rejected']);

        return back()->with('success', '❌ Produk ditolak.');
    }

    public function submit(Request $request)
    {
        if (!$request->has('products') || empty($request->products)) {
            return back()->with('error', '❌ Gagal menyimpan: data produk tidak terkirim!');
        }

        foreach ($request->products as $product) {
            $dp = DaftarProduk::find($product['daftar_produks_id'] ?? null);

            if (!$dp) {
                return back()->with('error', '❌ Produk tidak ditemukan di daftar_produks.');
            }

            Product::create([
                'user_id' => Auth::id(),
                'daftar_produks_id' => $dp->id,
                'stock' => $product['stock'] ?? 1,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('consignments.history')
            ->with('success', '✅ Produk berhasil diajukan!');
    }
}
