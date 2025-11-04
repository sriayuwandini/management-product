<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\StockLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function create()
    {
        $categories = \App\Models\Category::orderBy('nama_kategori')->get();
        return view('user.konsinyasi.index', compact('categories'));
    }

    public function submit(Request $request)
    {
        try {
            $data = $request->all();

            if (empty($data['products'])) {
                return back()->with('error', '❌ Data produk kosong atau tidak terkirim ke server.');
            }

            foreach ($data['products'] as $index => $product) {
                $imagePath = null;
                if ($request->hasFile("products.$index.image")) {
                    $imagePath = $request->file("products.$index.image")->store('products', 'public');
                }

                $newProduct = Product::create([
                    'user_id' => Auth::id(),
                    'name' => $product['name'] ?? '',
                    'category_id' => $product['category_id'] ?? null,
                    'description' => $product['description'] ?? '',
                    'price' => $product['price'] ?? 0,
                    'stock' => $product['stock'] ?? 0,
                    'image' => $imagePath,
                    'status' => 'pending',
                ]);

                StockLog::create([
                    'product_id' => $newProduct->id,
                    'user_id' => Auth::id(),
                    'quantity' => $product['stock'] ?? 0,
                    'type' => 'addition',
                    'description' => 'Pengajuan awal produk konsinyasi',
                ]);
            }

            return redirect()->route('consignments.history')
                ->with('success', '✅ Produk berhasil diajukan dan disimpan ke database!');
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
            'name' => $product->name,
            'category_id' => $product->category_id,
            'description' => $product->description,
            'price' => $product->price,
            'stock' => $lastStock,
            'image' => $product->image,
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

        $product->delete();

        StockLog::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'quantity' => 0,
            'type' => 'reduction', 
            'description' => 'Pengajuan produk dibatalkan',
        ]);

        return back()->with('success', '✅ Pengajuan produk berhasil dibatalkan.');
    }
}
