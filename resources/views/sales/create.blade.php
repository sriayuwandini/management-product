<x-app-layout>
    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Tambah Penjualan</h2>
                    <a href="{{ route('sales.index') }}"
                       class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg shadow">
                        Kembali
                    </a>
                </div>

                <form action="{{ route('sales.store') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Pilih Produk --}}
                    <div>
                        <label for="product_id" class="block text-sm font-medium text-gray-700">Pilih Produk</label>
                        <select id="product_id" name="product_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                    {{ $product->name }} - Rp {{ number_format($product->price, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jumlah --}}
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700">Jumlah</label>
                        <input type="number" id="quantity" name="quantity" min="1"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               required>
                        @error('quantity')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Harga otomatis tampil --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Harga per Unit</label>
                        <p id="price_display" class="text-gray-800 font-semibold">Rp 0</p>
                    </div>

                    {{-- Total otomatis tampil --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Total Harga</label>
                        <p id="total_display" class="text-gray-800 font-semibold">Rp 0</p>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-grey font-medium rounded-lg shadow">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const productSelect = document.getElementById('product_id');
        const quantityInput = document.getElementById('quantity');
        const priceDisplay = document.getElementById('price_display');
        const totalDisplay = document.getElementById('total_display');

        let selectedPrice = 0;

        productSelect.addEventListener('change', function() {
            selectedPrice = this.options[this.selectedIndex].getAttribute('data-price') || 0;
            priceDisplay.textContent = `Rp ${parseInt(selectedPrice).toLocaleString('id-ID')}`;
            updateTotal();
        });

        quantityInput.addEventListener('input', updateTotal);

        function updateTotal() {
            const qty = quantityInput.value || 0;
            const total = qty * selectedPrice;
            totalDisplay.textContent = `Rp ${parseInt(total).toLocaleString('id-ID')}`;
        }
    </script>
</x-app-layout>
