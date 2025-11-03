<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            ->paginate(10);

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

        $product = Product::findOrFail($request->product_id);

        $total = $request->quantity * $product->price;

        Sales::create([
            'user_id'     => Auth::id(),
            'product_id'  => $product->id,
            'quantity'    => $request->quantity,
            'price'       => $product->price, // ambil dari tabel product
            'total_price' => $total,
            'status'      => 'pending',
        ]);

        return redirect()->route('sales.index')
            ->with('success', 'Penjualan berhasil ditambahkan dan menunggu persetujuan admin.');
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
            'price'      => 'required|numeric|min:0',
            'status'     => 'required|in:pending,approved,rejected,completed',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Jika status diubah ke 'approved' dan sebelumnya bukan 'approved'
        if ($request->status === 'approved' && $sale->status !== 'approved') {

            // Cek stok cukup atau tidak
            if ($product->stock < $request->quantity) {
                return back()->withErrors(['quantity' => 'Stok produk tidak mencukupi.']);
            }

            // Kurangi stok
            $product->stock -= $request->quantity;
            $product->save();
        }

        // Jika status diubah dari 'approved' ke 'rejected', kembalikan stok
        if ($sale->status === 'approved' && $request->status === 'rejected') {
            $product->stock += $sale->quantity;
            $product->save();
        }

        // Update data penjualan
        $sale->update([
            'product_id'  => $request->product_id,
            'quantity'    => $request->quantity,
            'price'       => $product->price,
            'total_price' => $request->quantity * $product->price,
            'status'      => $request->status,
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
