<x-app-layout>
    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
                
                {{-- Header --}}
                <div class="px-6 py-4 border-b bg-gradient-to-r from-indigo-600 to-indigo-700 text-gray">
                    <h2 class="text-2xl font-bold">
                        ✏️ Edit Penjualan <span class="opacity-90">#{{ $sale->invoice_number }}</span>
                    </h2>
                </div>

                <form action="{{ route('sales.update', $sale->id) }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                        <table class="w-full text-sm text-gray-700">
                            <thead class="bg-indigo-100 text-gray-800 uppercase text-xs tracking-wide">
                                <tr>
                                    <th class="border px-4 py-3 text-left">Produk</th>
                                    <th class="border px-4 py-3 text-center">Harga</th>
                                    <th class="border px-4 py-3 text-center">Qty Order</th>
                                    <th class="border px-4 py-3 text-center">Qty Delivery</th>
                                    <th class="border px-4 py-3 text-center">Qty Sold</th>
                                    <th class="border px-4 py-3 text-center">Subtotal</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                @foreach ($sale->details as $index => $detail)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 font-medium text-gray-800">
                                            {{ $detail->product->name }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            Rp{{ number_format($detail->price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <input type="number" 
                                                name="details[{{ $index }}][quantity_order]"
                                                value="{{ $detail->quantity_order }}"
                                                class="w-24 text-center border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <input type="number" 
                                                name="details[{{ $index }}][quantity_delivery]"
                                                value="{{ $detail->quantity_delivery }}"
                                                class="w-24 text-center border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <input type="number" 
                                                name="details[{{ $index }}][quantity_sold]"
                                                value="{{ $detail->quantity_sold }}"
                                                class="w-24 text-center border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                        </td>
                                        <td class="px-4 py-3 text-center font-semibold text-indigo-700">
                                            Rp{{ number_format($detail->subtotal, 0, ',', '.') }}
                                        </td>
                                        <input type="hidden" name="details[{{ $index }}][id]" value="{{ $detail->id }}">
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-end items-center space-x-3">
                        <a href="{{ route('sales.index') }}"
                           class="bg-gray-600 hover:bg-indigo-700 text-gray-800 px-5 py-2 rounded-lg shadow transition">
                            ← Kembali
                        </a>
                        <button type="submit"
                            class="bg-gray-600 hover:bg-indigo-700 text-gray px-5 py-2 rounded-lg shadow transition">
                            💾 Simpan Perubahan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
