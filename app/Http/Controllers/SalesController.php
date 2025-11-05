<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class SalesController extends Controller
{
    /**
     * Tampilkan semua data penjualan milik sales yang login.
     */
    public function index()
    {
        $sales = Sales::with('product')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10)
            ->through(function ($sale) {
                $sale->formatted_date = Carbon::parse($sale->created_at)
                    ->format('d M Y H:i'); // contoh: 03 Nov 2025 19:30
                return $sale;
            });

        return view('sales.index', compact('sales'));
    }

    /**
     * Form untuk menambahkan penjualan baru.
     */
    public function create()
    {
        $products = Product::all();
        return view('sales.create', compact('products'));
    }

    /**
     * Simpan data penjualan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = \App\Models\Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Stok produk tidak mencukupi.');
        }

        $total = $product->price * $request->quantity;

        Sales::create([
            'user_id'     => Auth::id(),
            'product_id'  => $product->id,
            'quantity'    => $request->quantity,
            'price'       => $product->price,
            'total_price' => $total,
        ]);

        // Kurangi stok produk
        $product->decrement('stock', $request->quantity);

        return redirect()->route('sales.index')->with('success', 'Penjualan berhasil ditambahkan dan stok diperbarui!');
    }


    /**
     * Tampilkan detail penjualan.
     */
    public function show(Sales $sale)
    {
        $this->authorizeSale($sale);
        return view('sales.show', compact('sale'));
    }

    /**
     * Form edit penjualan.
     */
    public function edit(Sales $sale)
    {
        $this->authorizeSale($sale);
        $products = Product::all();
        return view('sales.edit', compact('sale', 'products'));
    }

    /**
     * Update penjualan.
     */
    public function update(Request $request, Sales $sale)
    {
        $this->authorizeSale($sale);

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $price = $product->price;
        $total = $price * $request->quantity;

        $sale->update([
            'product_id'  => $product->id,
            'quantity'    => $request->quantity,
            'price'       => $price,
            'total_price' => $total,
        ]);

        return redirect()->route('sales.index')->with('success', 'Data penjualan berhasil diperbarui.');
    }


    /**
     * Hapus penjualan.
     */
    public function destroy(Sales $sale)
    {
        $this->authorizeSale($sale);
        $sale->delete();

        return redirect()->route('sales.index')->with('success', 'Data penjualan berhasil dihapus.');
    }

    /**
     * Cegah user mengakses data sales milik orang lain.
     */
    private function authorizeSale(Sales $sale)
    {
        if ($sale->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses data ini.');
        }
    }

}
