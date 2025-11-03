<x-app-layout>
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">📊 Laporan Penjualan</h2>

            <div class="bg-white p-6 rounded-lg shadow mb-6">
                <form method="GET" action="{{ route('sales.reports.index') }}" class="flex flex-col md:flex-row md:items-end gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                               class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Tanggal Selesai</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                               class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <button type="submit"
                                class="px-4 py-1.5 bg-indigo-800 text-gray-800 rounded-md hover:bg-indigo-700 shadow">
                            Filter
                        </button>
                        <a href="{{ route('sales.reports.index') }}"
                           class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-indigo-700 shadow">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-green-100 text-green-700 p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold">Total Penjualan</h3>
                    <p class="text-xl font-bold mt-2">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</p>
                </div>
                <div class="bg-blue-100 text-blue-700 p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold">Total Transaksi</h3>
                    <p class="text-xl font-bold mt-2">{{ $sales->total() }}</p>
                </div>
                <div class="bg-yellow-100 text-yellow-700 p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold">Disetujui</h3>
                    <p class="text-xl font-bold mt-2">{{ $approvedSales }}</p>
                </div>
            </div>

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-gray-800">📊 Laporan Penjualan</h2>
                <a href="{{ route('sales.reports.pdf', request()->query()) }}"
                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 shadow">
                🧾 Unduh PDF
                </a>
            </div>

            {{-- 📋 Tabel Laporan --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full w-full table-auto text-sm text-left text-gray-700 border-collapse">
                    <thead class="bg-gray-100 text-gray-800 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3">Jumlah</th>
                            <th class="px-4 py-3">Harga</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sales as $sale)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $loop->iteration + ($sales->currentPage() - 1) * $sales->perPage() }}</td>
                                <td class="px-4 py-3">{{ $sale->product->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $sale->quantity }}</td>
                                <td class="px-4 py-3">Rp {{ number_format($sale->price, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 font-semibold">Rp {{ number_format($sale->total_price, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if ($sale->status === 'approved') bg-green-100 text-green-700
                                        @elseif ($sale->status === 'rejected') bg-red-100 text-red-700
                                        @else bg-yellow-100 text-yellow-700 @endif">
                                        {{ ucfirst($sale->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $sale->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-gray-500">
                                    Tidak ada data penjualan ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 📄 Pagination --}}
            <div class="mt-6">
                {{ $sales->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
