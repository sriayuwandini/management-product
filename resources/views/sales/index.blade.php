<x-app-layout>
    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Daftar Penjualan</h2>
                <a href="{{ route('sales.create') }}"
                   class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-black rounded-lg shadow">
                    + Tambah Penjualan
                </a>
            </div>

            <div class="bg-white shadow rounded-lg overflow-x-auto">
                <table class="w-full border-collapse text-sm text-left text-gray-700">
                    <thead class="bg-gray-800 text-white uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">No</th>
                            <th class="px-6 py-3">Produk</th>
                            <th class="px-6 py-3">Jumlah</th>
                            <th class="px-6 py-3">Harga</th>
                            <th class="px-6 py-3">Total</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @forelse ($sales as $index => $sale)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3">{{ $loop->iteration }}</td>
                                <td class="px-6 py-3">{{ $sale->product->name ?? '-' }}</td>
                                <td class="px-6 py-3">{{ $sale->quantity }}</td>
                                <td class="px-6 py-3">Rp {{ number_format($sale->price, 0, ',', '.') }}</td>
                                <td class="px-6 py-3 font-semibold text-gray-800">
                                    Rp {{ number_format($sale->total_price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if ($sale->status === 'approved') bg-green-100 text-green-700
                                        @elseif ($sale->status === 'rejected') bg-red-100 text-red-700
                                        @else bg-yellow-100 text-yellow-700 @endif">
                                        {{ ucfirst($sale->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-center space-x-2">
                                    <a href="{{ route('sales.edit', $sale->id) }}"
                                    class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                                    <form action="{{ route('sales.destroy', $sale->id) }}" method="POST"
                                        class="inline-block"
                                        onsubmit="return confirm('Hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-800 font-medium">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-gray-500">
                                    Belum ada data penjualan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $sales->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
