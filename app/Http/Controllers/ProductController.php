<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
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

                \App\Models\Product::create([
                    'user_id' => Auth::id(),
                    'name' => $product['name'] ?? '',
                    'category_id' => $product['category_id'] ?? null,
                    'description' => $product['description'] ?? '',
                    'price' => $product['price'] ?? 0,
                    'stock' => $product['stock'] ?? 0,
                    'image' => $imagePath,
                    'status' => 'pending',
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

        return view('user.konsinyasi.history', compact('products'));
    }

    public function resubmit(Product $product)
    {
        if ($product->user_id !== Auth::id()) {
            return back()->with('error', '❌ Anda tidak berhak mengajukan kembali produk ini.');
        }

        Product::create([
            'user_id' => Auth::id(),
            'name' => $product->name,
            'category_id' => $product->category_id,
            'description' => $product->description,
            'price' => $product->price,
            'stock' => $product->stock,
            'image' => $product->image,
            'status' => 'pending',
        ]);

        return back()->with('success', '✅ Produk berhasil diajukan kembali!');
    }

    public function cancel(Product $product)
    {
        if ($product->user_id !== Auth::id()) {
            return back()->with('error', '❌ Anda tidak berhak membatalkan pengajuan ini.');
        }

        $product->delete();

        return back()->with('success', '✅ Pengajuan produk berhasil dibatalkan.');
    }



}
