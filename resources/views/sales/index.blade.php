<x-app-layout>
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Daftar Penjualan</h2>
                <a href="{{ route('sales.create') }}"
                    class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-gray font-medium rounded-lg shadow">
                    + Tambah Penjualan
                </a>
            </div>

            <div class="bg-white shadow-md rounded-lg overflow-x-auto">
                <table class="w-full border-collapse text-sm text-left text-gray-700">
                    <thead class="bg-gray-800 text-white uppercase text-xs">
                        <tr>
                            <th class="px-5 py-3">No</th>
                            <th class="px-5 py-3">Invoice</th>
                            <th class="px-5 py-3">Sales</th>
                            <th class="px-5 py-3">Customer</th>
                            <th class="px-5 py-3">Jumlah Produk</th>
                            <th class="px-5 py-3">Tanggal</th>
                            <th class="px-5 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @forelse ($sales as $sale)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-3 text-gray-600">{{ $loop->iteration }}</td>
                                <td class="px-5 py-3 font-semibold text-gray-800">
                                    {{ $sale->invoice_number }}
                                </td>
                                <td class="px-5 py-3">{{ Auth::user()->name ?? 'Administrator' }}</td>
                                <td class="px-5 py-3">
                                    @php
                                        $productUser = optional($sale->details->first()->product->user ?? null)->name;
                                    @endphp

                                    {{ $productUser ?? '-' }}
                                </td>
                                <td class="px-5 py-3 text-gray-700">
                                    {{ $sale->details->count() }} item
                                </td>
                                <td class="px-5 py-3 text-gray-600">
                                    {{ $sale->created_at->translatedFormat('d M Y H:i') }}
                                </td>
                                <td class="px-5 py-3 text-center space-x-2">
                                    <a href="{{ route('sales.edit', $sale->id) }}"
                                        class="text-indigo-600 hover:text-indigo-800 font-medium">
                                        Edit
                                    </a>

                                    <form action="{{ route('sales.destroy', $sale->id) }}" method="POST"
                                        class="inline-block"
                                        onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-800 font-medium">
                                            Hapus
                                        </button>
                                    </form>

                                    <button 
                                        type="button"
                                        data-id="{{ $sale->id }}"
                                        class="text-gray-700 hover:text-black font-medium"
                                        onclick="toggleDetail(this.dataset.id)">
                                        Detail
                                    </button>
                                </td>
                            </tr>

                
                            <tr id="detail-{{ $sale->id }}" class="hidden bg-gray-50">
                                <td colspan="8" class="p-4">
                                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                                        <table class="w-full text-sm">
                                            <thead class="bg-gray-100 text-gray-700">
                                                <tr>
                                                    <th class="px-3 py-2 border">Produk</th>
                                                    <th class="px-3 py-2 border text-center">Harga</th>
                                                    <th class="px-3 py-2 border text-center">Qty Order</th>
                                                    <th class="px-3 py-2 border text-center">Qty Delivery</th>
                                                    <th class="px-3 py-2 border text-center">Qty Sold</th>
                                                    <th class="px-3 py-2 border text-center">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($sale->details as $detail)
                                                    <tr>
                                                        <td class="px-3 py-2 border">
                                                            {{ $detail->product->name ?? '-' }}
                                                        </td>
                                                        <td class="px-3 py-2 border text-center">
                                                            Rp {{ number_format($detail->price, 0, ',', '.') }}
                                                        </td>
                                                        <td class="px-3 py-2 border text-center">
                                                            {{ $detail->quantity_order }}
                                                        </td>
                                                        <td class="px-3 py-2 border text-center">
                                                            {{ $detail->quantity_delivery }}
                                                        </td>
                                                        <td class="px-3 py-2 border text-center">
                                                            {{ $detail->quantity_sold }}
                                                        </td>
                                                        <td class="px-3 py-2 border text-center font-semibold">
                                                            Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-6 text-gray-500">
                                    Belum ada data penjualan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $sales->links() }}
            </div>

        </div>
    </div>

    <script>
        function toggleDetail(id) {
            const row = document.getElementById('detail-' + id);
            row.classList.toggle('hidden');
        }
    </script>
</x-app-layout>
