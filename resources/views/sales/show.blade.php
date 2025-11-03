<x-app-layout>
    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Detail Penjualan</h2>
                    <a href="{{ route('sales.index') }}"
                       class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg shadow">
                        Kembali
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-gray-600 text-sm">Nama Produk</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $sale->product->name ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-600 text-sm">Harga per Unit</p>
                        <p class="text-lg font-semibold text-gray-800">
                            Rp {{ number_format($sale->price, 0, ',', '.') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-600 text-sm">Jumlah Terjual</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $sale->quantity }}</p>
                    </div>

                    <div>
                        <p class="text-gray-600 text-sm">Total Harga</p>
                        <p class="text-lg font-semibold text-gray-800">
                            Rp {{ number_format($sale->total_price, 0, ',', '.') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-600 text-sm">Tanggal Penjualan</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $sale->created_at->format('d M Y, H:i') }}</p>
                    </div>

                    <div>
                        <p class="text-gray-600 text-sm">Status</p>
                        <span class="px-2 py-1 text-sm rounded-full
                            @if ($sale->status === 'approved') bg-green-100 text-green-700
                            @elseif ($sale->status === 'rejected') bg-red-100 text-red-700
                            @else bg-yellow-100 text-yellow-700 @endif">
                            {{ ucfirst($sale->status) }}
                        </span>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <a href="{{ route('sales.edit', $sale->id) }}"
                       class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow">
                        Edit Data
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
