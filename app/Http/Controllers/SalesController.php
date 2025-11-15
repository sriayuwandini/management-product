<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Models\Product;
use App\Models\SalesDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


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
                    ->format('d M Y H:i'); 
                return $sale;
            });

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $products = Product::all();
        return view('sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'user_id'       => 'required|exists:users,id',
            'products'      => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
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
                $product = Product::findOrFail($item['product_id']);
                $subtotal = $product->price;

                SalesDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity_order' => 1,
                    'quantity_delivery' => 0,
                    'quantity_sold' => 0,
                    'price' => $product->price,
                    'subtotal' => $subtotal,
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
    
        $sale->load('product');
    
        return view('sales.show', compact('sale'));
    }
    

    public function edit(Sales $sale)
{
    $this->authorizeSale($sale);

    $products = Product::all();

    $sale->load('details.product');

    return view('sales.edit', compact('sale', 'products'));
}


public function update(Request $request, Sales $sale)
{
    $this->authorizeSale($sale);

    $request->validate([
        'details' => 'required|array|min:1',
        'details.*.id' => 'required|exists:sales_details,id',
        'details.*.quantity_order' => 'nullable|integer|min:0',
        'details.*.quantity_delivery' => 'nullable|integer|min:0',
        'details.*.quantity_sold' => 'nullable|integer|min:0',
    ]);

    DB::transaction(function () use ($request, $sale) {
        $total = 0;

        foreach ($request->details as $detailData) {
            $detail = SalesDetail::with('product')->findOrFail($detailData['id']);
            $product = $detail->product;

            $oldQtySold = $detail->quantity_sold;
            $newQtySold = $detailData['quantity_sold'] ?? 0;
            $difference = $newQtySold - $oldQtySold;

            if ($difference > 0) {
                if ($product->stock < $difference) {
                    throw new \Exception("Stok produk {$product->name} tidak cukup!");
                }
                $product->decrement('stock', $difference);
            } elseif ($difference < 0) {
                $product->increment('stock', abs($difference));
            }

            $subtotal = $product->price * $newQtySold;

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

    return redirect()->route('sales.index')->with('success', 'Data penjualan berhasil diperbarui dan stok diperbaharui.');
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
        $products = Product::where('name', 'like', "%{$query}%")->limit(10)->get();

        return response()->json($products);
    }

}
