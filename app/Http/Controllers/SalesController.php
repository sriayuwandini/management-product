<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Models\DaftarProduk;
use App\Models\SalesDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function index()
    {

        $sales = Sales::with('details.produk.stockLogs.user')
        ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10)
            ->through(function ($sale) {
                $sale->formatted_date = Carbon::parse($sale->created_at)
                    ->format('d M Y H:i'); 
                return $sale;
            });

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $products = DaftarProduk::all();
        return view('sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'products'      => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:daftar_produks,id',
        ]);

        DB::transaction(function () use ($request) {
            $sale = Sales::create([
                'user_id'        => Auth::id(),
                'invoice_number' => 'INV-' . now()->format('YmdHis'),
                'customer_name'  => $request->customer_name,
                'sale_date'      => $request->sale_date,
                'total_amount'   => 0,
            ]);

            $total = 0;

            foreach ($request->products as $item) {
                $product = DaftarProduk::findOrFail($item['product_id']);
                $subtotal = $product->harga;

                SalesDetail::create([
                    'sale_id'          => $sale->id,
                    'product_id'       => $product->id,
                    'quantity_order'   => 1,
                    'quantity_delivery'=> 0,
                    'quantity_sold'    => 0,
                    'price'            => $product->harga,
                    'subtotal'         => $subtotal,
                ]);

                $total += $subtotal;
            }

            $sale->update(['total_amount' => $total]);
        });

        return redirect()->route('sales.index')->with('success', 'Penjualan berhasil disimpan.');
    }

    public function show(Sales $sale)
    {
        $this->authorizeSale($sale);
        $sale->load('details.produk');

        return view('sales.show', compact('sale'));
    }

    public function edit(Sales $sale)
    {
        $this->authorizeSale($sale);

        $products = DaftarProduk::all();
        $sale->load('details.produk');

        return view('sales.edit', compact('sale', 'products'));
    }

    public function update(Request $request, Sales $sale)
    {
        $this->authorizeSale($sale);

        DB::transaction(function () use ($request, $sale) {
            $total = 0;

            foreach ($request->details as $detailData) {
                $detail = SalesDetail::with('produk')->findOrFail($detailData['id']);
                $produk = $detail->produk;

                $oldQtySold = $detail->quantity_sold;
                $newQtySold = max(0, (int) ($detailData['quantity_sold'] ?? 0));
                $difference = $newQtySold - $oldQtySold;

                // 🔥 AMBIL STOK DARI TABEL products
                $productStock = Product::where('daftar_produks_id', $produk->id)
                    ->where('stock', '>', 0)
                    ->lockForUpdate()
                    ->first();

                if ($difference > 0) {
                    if (!$productStock || $productStock->stock < $difference) {
                        throw new \Exception("Stok produk {$produk->nama_produk} tidak cukup!");
                    }
                    $productStock->decrement('stock', $difference);
                } elseif ($difference < 0 && $productStock) {
                    $productStock->increment('stock', abs($difference));
                }

                // 🔥 SUBTOTAL OTOMATIS DARI QTY SOLD
                $subtotal = $produk->harga * $newQtySold;

                $detail->update([
                    'quantity_order'    => $detailData['quantity_order'] ?? 0,
                    'quantity_delivery' => $detailData['quantity_delivery'] ?? 0,
                    'quantity_sold'     => $newQtySold,
                    'subtotal'          => $subtotal,
                ]);

                $total += $subtotal;
            }

            $sale->update(['total_amount' => $total]);
        });

        return redirect()->route('sales.index')
            ->with('success', 'Data penjualan berhasil diperbarui.');
    }

    public function destroy(Sales $sale)
    {
        $this->authorizeSale($sale);
        $sale->delete();

        return redirect()->route('sales.index')->with('success', 'Data penjualan berhasil dihapus.');
    }

    private function authorizeSale(Sales $sale)
    {
        if ($sale->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses data ini.');
        }
    }

    public function searchProduct(Request $request)
    {
        $query = $request->get('query');

        $products = DaftarProduk::where('nama_produk', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        return response()->json($products);
    }

    public function storePayment(Request $request, Sales $sale)
{
    $this->authorizeSale($sale);

    // 1️⃣ VALIDASI QTY SOLD
    $totalQtySold = $sale->details()->sum('quantity_sold');
    if ($totalQtySold < 1) {
        return back()
            ->withErrors([
                'payment' => 'Qty sold minimal harus 1 sebelum melakukan pembayaran.'
            ])
            ->withInput()
            ->with('open_detail', $sale->id);
    }

    // 2️⃣ VALIDASI FORM
    $request->validate([
        'payment_method' => 'required|in:cash,transfer',
        'payment_proof'  => 'required_if:payment_method,transfer|image|max:2048',
    ], [
        'payment_proof.required_if' => 'Bukti transfer wajib diunggah.',
    ]);

    // 3️⃣ SIMPAN DATA PEMBAYARAN
    $data = [
        'payment_method' => $request->payment_method,
        'payment_at'     => now(),
    ];

    if ($request->payment_method === 'transfer') {
        $data['payment_proof'] = $request->file('payment_proof')
            ->store('payment_proofs', 'public');
    }

    $sale->update($data);

    // 4️⃣ SUKSES
    return back()
        ->with('success', 'Pembayaran berhasil diproses.')
        ->with('open_detail', $sale->id);
}


}
