<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Sales;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {

        $query = Sales::with('product')
            ->where('user_id', Auth::id());

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $sales = $query->latest()->paginate(10);
        $totalPenjualan = $sales->sum('total_price');
        $approvedSales = $sales->where('status', 'approved')->count();

        return view('sales.reports.index', compact('sales', 'totalPenjualan', 'approvedSales'));
    }

    public function exportPdf(Request $request)
    {
        $query = Sales::with('product')
            ->where('user_id', Auth::id());

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $sales = $query->latest()->get();
        $totalPenjualan = $sales->sum('total_price');
        $approvedSales = $sales->where('status', 'approved')->count();

        $pdf = Pdf::loadView('sales.reports.pdf', [
            'sales' => $sales,
            'totalPenjualan' => $totalPenjualan,
            'approvedSales' => $approvedSales,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('laporan_penjualan_' . date('Ymd_His') . '.pdf');
    }
}
