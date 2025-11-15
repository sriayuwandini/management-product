<?php

namespace App\Http\Controllers;

use App\Models\DaftarProduk;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\StockLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function create(Request $request)
        {
            $categories = \App\Models\Category::orderBy('nama_kategori')->get();

            $query = DaftarProduk::with('category')->orderBy('nama_produk');

            if ($request->has('q') && $request->q != '') {
                $search = $request->q;
                $query->where(function ($q) use ($search) {
                    $q->where('nama_produk', 'like', "%{$search}%")
                    ->orWhere('kode_produk', 'like', "%{$search}%");
                });
            }

            $daftarProduks = $query->paginate(10)->appends($request->except('page'));

            return view('user.konsinyasi.index', compact('categories', 'daftarProduks'));
        }


    public function submit(Request $request)
    {
        try {
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
        } catch (\Throwable $e) {
            return back()->with('error', '❌ Gagal menyimpan: ' . $e->getMessage());
        }
    }



    public function history()
    {
        $products = Product::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        foreach ($products as $product) {
            $latestLog = StockLog::where('product_id', $product->id)
                ->orderBy('created_at', 'desc')
                ->first();

            $product->final_stock = $latestLog->quantity ?? $product->stock;
        }

        return view('user.konsinyasi.history', compact('products'));
    }


    public function resubmit(Product $product)
    {
        if ($product->user_id !== Auth::id()) {
            return back()->with('error', '❌ Anda tidak berhak mengajukan kembali produk ini.');
        }

        $lastStock = StockLog::where('product_id', $product->id)
            ->orderBy('created_at', 'desc')
            ->value('quantity') ?? $product->stock;

        $newProduct = Product::create([
            'user_id' => Auth::id(),
            'daftar_produks_id' => $product->daftar_produks_id, 
            'stock' => $lastStock,
            'status' => 'pending',
        ]);

        StockLog::create([
            'product_id' => $newProduct->id,
            'user_id' => Auth::id(),
            'quantity' => $lastStock,
            'type' => 'addition',
            'description' => 'Pengajuan ulang produk konsinyasi',
        ]);

        return back()->with('success', '✅ Produk berhasil diajukan kembali!');
    }


    public function cancel(Product $product)
    {
        if ($product->user_id !== Auth::id()) {
            return back()->with('error', '❌ Anda tidak berhak membatalkan pengajuan ini.');
        }

        StockLog::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'quantity' => 0,
            'type' => 'reduction', 
            'description' => 'Pengajuan produk dibatalkan',
        ]);

        $product->delete();

        return back()->with('success', '✅ Pengajuan produk berhasil dibatalkan.');
    }

    public function salesIndex()
    {
        $products = Product::where('stock', '>', 0)
        ->orderBy('name', 'asc')
        ->paginate(10);

    return view('sales.products', compact('products'));
    }

}
