<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\StockLog;
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
        $baruUsers = User::whereHas('products', function ($query) {
            $query->where('status', 'pending')->where('created_at', '>=', now()->subDays(3));
        })
            ->with(['products' => function ($q) {
                $q->where('status', 'pending')
                    ->where('created_at', '>=', now()->subDays(3))
                    ->orderBy('created_at', 'desc');
            }, 'products.category'])
            ->orderBy('name', 'asc')
            ->paginate(5, ['*'], 'baru_page');

        $lamaUsers = User::whereHas('products', function ($query) {
            $query->where('status', 'pending')->where('created_at', '<', now()->subDays(3));
        })
            ->with(['products' => function ($q) {
                $q->where('status', 'pending')
                    ->where('created_at', '<', now()->subDays(3))
                    ->orderBy('created_at', 'desc');
            }, 'products.category'])
            ->orderBy('name', 'asc')
            ->paginate(5, ['*'], 'lama_page');

        return view('admin_produksi.validasi_konsinyasi', compact('baruUsers', 'lamaUsers'));
    }

    public function riwayatDisetujui()
    {
        $users = User::whereHas('products', function ($q) {
            $q->where('status', 'approved');
        })->orderBy('name')->paginate(10);

        return view('admin_produksi.riwayat_disetujui', compact('users'));
    }

    public function riwayatDitolak()
    {
        $users = User::whereHas('products', function ($q) {
            $q->where('status', 'rejected');
        })
            ->with(['products' => function ($q) {
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

        if ($request->status === 'approved') {
            // 🔹 Cari produk existing dengan nama sama & status approved
            $existingProduct = Product::where('user_id', $product->user_id)
                ->where('name', $product->name)
                ->where('status', 'approved')
                ->first();

            if ($existingProduct) {
                // Update stok produk existing
                $existingProduct->update([
                    'stock' => $existingProduct->stock + $product->stock,
                ]);

                // Catat ke log
                StockLog::create([
                    'product_id' => $existingProduct->id,
                    'user_id' => $product->user_id,
                    'quantity' => $product->stock,
                    'type' => 'addition', // ✅ stok bertambah
                    'description' => 'Penambahan stok dari pengajuan baru',
                ]);

                // Hapus produk pending setelah diproses
                $product->delete();
            } else {
                // Jika produk belum ada, langsung ubah status jadi approved
                $product->update(['status' => 'approved']);

                // Catat ke log
                StockLog::create([
                    'product_id' => $product->id,
                    'user_id' => $product->user_id,
                    'quantity' => $product->stock,
                    'type' => 'addition', // ✅ stok bertambah (produk baru disetujui)
                    'description' => 'Persetujuan awal produk konsinyasi',
                ]);
            }

            return back()->with('success', '✅ Produk disetujui dan stok berhasil diperbarui!');
        }

        // 🔹 Jika ditolak
        $product->update(['status' => 'rejected']);

        StockLog::create([
            'product_id' => $product->id,
            'user_id' => $product->user_id,
            'quantity' => 0,
            'type' => 'reduction', // ✅ ditolak → stok tidak bertambah
            'description' => 'Produk ditolak oleh admin',
        ]);

        return back()->with('success', '❌ Produk ditolak.');
    }
}
