<x-app-layout>
    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6 grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="col-span-1 border-r pr-4">
                    <div class="border-b mb-3">
                        <ul class="flex text-sm font-medium text-gray-600">
                            <li class="mr-4">
                                <button type="button" class="border-b-2 border-indigo-600 pb-1 text-indigo-600">
                                    Cari Produk
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Produk</label>
                        <select id="productSelect" multiple
                            class="w-full border rounded px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        data-price="{{ $product->price }}"
                                        data-user="{{ $product->user->name ?? 'Tidak diketahui' }}">
                                    {{ $product->name }} - Rp{{ number_format($product->price, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Hasil Pencarian</h3>
                        <ul id="searchResults" class="text-sm text-gray-700 space-y-2">
                            <li class="italic text-gray-500">Belum ada hasil.</li>
                        </ul>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
                        <ul class="list-disc pl-5 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="col-span-2">
                    <form action="{{ route('sales.store') }}" method="POST" id="saleForm">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                        <input type="hidden" name="sale_date" value="{{ now()->format('Y-m-d') }}">

                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Nama Customer</label>
                                <input type="text" id="customerName" name="customer_name"
                                    class="bg-gray-100 border rounded px-3 py-2 text-sm" readonly>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Nama Sales</label>
                                <input type="text" name="sales_name" value="{{ Auth::user()->name ?? 'Administrator' }}"
                                    class="bg-gray-100 border rounded px-3 py-2 text-sm" readonly>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Tanggal: {{ now()->translatedFormat('d F Y') }}</p>
                            </div>
                        </div>

                        <div class="overflow-x-auto mb-4 border rounded-lg">
                            <table class="w-full text-sm text-left border-collapse">
                                <thead class="bg-gray-100 text-gray-700">
                                    <tr>
                                        <th class="px-3 py-2 border">Nama Produk</th>
                                        <th class="px-3 py-2 border w-32">Harga</th>
                                        <th class="px-3 py-2 border w-24 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="productRows">
                                    <tr class="text-center text-gray-500">
                                        <td colspan="3" class="py-3 italic">Belum ada produk ditambahkan.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="flex justify-end gap-2">
                            <button type="reset"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow">
                                Kosongkan
                            </button>
                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-grey px-4 py-2 rounded shadow">
                                Simpan Penjualan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
    const customerInput = document.getElementById('customerName');
    const selectElement = document.getElementById('productSelect');

    const select = new TomSelect(selectElement, {
        plugins: ['remove_button'],
        placeholder: "Cari atau pilih produk...",
        persist: false,
        create: false,
        onChange: function(values) {
            updateCustomerName(values);
        }
    });

    function updateCustomerName(values) {
        if (values.length === 0) {
            customerInput.value = '';
            return;
        }

        let userNames = new Set();

        values.forEach(value => {
            const option = selectElement.querySelector(`option[value="${value}"]`);
            if (option && option.dataset.user) {
                userNames.add(option.dataset.user);
            }
        });

        customerInput.value = Array.from(userNames).join(', ');
    }
});

        const tbody = document.getElementById('productRows');

        function updateProductTable(values) {
            tbody.innerHTML = ''; 

            if (values.length === 0) {
                tbody.innerHTML = `<tr class="text-center text-gray-500">
                    <td colspan="3" class="py-3 italic">Belum ada produk ditambahkan.</td>
                </tr>`;
                return;
            }

            values.forEach((id, index) => {
                const option = select.options[id];
                const name = option.text.split(' - ')[0];
                const priceText = option.text.split(' - ')[1];
                const price = option.$option.dataset.price;

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="px-3 py-2 border">${name}
                        <input type="hidden" name="products[${index}][product_id]" value="${id}">
                        <input type="hidden" name="products[${index}][quantity_order]" value="1">
                    </td>
                    <td class="px-3 py-2 border">${priceText}</td>
                    <td class="px-3 py-2 border text-center">
                        <button type="button"
                            class="remove-row bg-red-500 hover:bg-red-600 text-grey text-xs px-2 py-1 rounded">
                            Hapus
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-row')) {
                const tr = e.target.closest('tr');
                const input = tr.querySelector('input[name*="[product_id]"]');
                const productId = input.value;

                select.removeItem(productId);
                tr.remove();

                if (tbody.children.length === 0) {
                    tbody.innerHTML = `<tr class="text-center text-gray-500">
                        <td colspan="3" class="py-3 italic">Belum ada produk ditambahkan.</td>
                    </tr>`;
                }
            }
        });
    </script>
</x-app-layout>
