<x-app-layout>
    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Daftar Produk Tersedia</h2>
            </div>

            <div class="bg-white shadow-md rounded-lg overflow-x-auto">
                <table class="w-full border-collapse text-sm text-left text-gray-700">
                    <thead class="bg-gray-800 text-white uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">No</th>
                            <th class="px-6 py-3">Nama Produk</th>
                            <th class="px-6 py-3">Harga</th>
                            <th class="px-6 py-3">Stok</th>
                            <th class="px-6 py-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($products as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3">{{ $loop->iteration }}</td>
                                <td class="px-6 py-3 font-medium text-gray-800">{{ $product->name }}</td>
                                <td class="px-6 py-3">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-3">
                                    @if ($product->stock > 10)
                                        <span class="text-green-700 font-semibold">{{ $product->stock }}</span>
                                    @elseif ($product->stock > 0)
                                        <span class="text-yellow-600 font-semibold">{{ $product->stock }}</span>
                                    @else
                                        <span class="text-red-600 font-semibold">Habis</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-gray-600">
                                    {{ $product->description ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-gray-500">
                                    Belum ada produk yang tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $products->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
