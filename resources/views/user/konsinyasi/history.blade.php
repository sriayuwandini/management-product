<x-app-layout>
    <div class="py-8 px-6 lg:px-12">

        @if(session('success'))
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil!",
                        text: "{{ session('success') }}",
                        timer: 2000,
                        showConfirmButton: false
                    });
                });
            </script>
        @endif

        @if(session('error'))
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal!",
                        text: "{{ session('error') }}",
                        timer: 2500,
                        showConfirmButton: false
                    });
                });
            </script>
        @endif

        <div class="text-gray-500 py-2 bg-white rounded-xl shadow-md mb-4">
            <div class="p-4 flex justify-between items-center">
                <h5 class="font-semibold text-gray-700">📦 Riwayat Pengajuan Konsinyasi Anda</h5>
            </div>
        </div>

        @if($products->isEmpty())
            <div class="text-center text-gray-500 py-10 bg-white rounded-xl shadow-md border border-gray-200">
                Belum ada pengajuan produk.
            </div>
        @else
            <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-700">
                        <thead class="bg-gray-100 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 font-semibold uppercase text-xs tracking-wider">No</th>
                                <th class="px-6 py-3 font-semibold uppercase text-xs tracking-wider">Nama Produk</th>
                                <th class="px-6 py-3 font-semibold uppercase text-xs tracking-wider">Kategori</th>
                                <th class="px-6 py-3 font-semibold uppercase text-xs tracking-wider">Harga</th>
                                <th class="px-6 py-3 font-semibold uppercase text-xs tracking-wider">Stok</th>
                                <th class="px-6 py-3 font-semibold uppercase text-xs tracking-wider">Status</th>
                                <th class="px-6 py-3 font-semibold uppercase text-xs tracking-wider">Tanggal Diajukan</th>
                                <th class="px-6 py-3 text-center font-semibold uppercase text-xs tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $index => $product)
                                @php
                                    $statusMap = [
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                        'pending'  => 'Menunggu',
                                    ];
                                    $statusLabel = $statusMap[$product->status] ?? ucfirst($product->status);
                                @endphp

                                <tr class="border-b hover:bg-gray-50 transition">
                                    <td class="px-6 py-3 text-gray-900">{{ $products->firstItem() + $index }}</td>
                                    <td class="px-6 py-3 font-medium text-gray-900">{{ $product->name }}</td>
                                    <td class="px-6 py-3">{{ $product->category->nama_kategori ?? '-' }}</td>
                                    <td class="px-6 py-3">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td class="px-6 py-3">
                                        {{ $product->latestStockLog->quantity ?? $product->stock }}
                                    </td>
                                    <td class="px-6 py-3">
                                        @if($product->status == 'pending')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">Menunggu</span>
                                        @elseif($product->status == 'approved')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Disetujui</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Ditolak</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-gray-600">{{ $product->created_at->format('d M Y') }}</td>
                                    <td class="px-6 py-3 text-center">
                                        <button data-modal-target="detailModal" data-modal-toggle="detailModal"
                                            data-id="{{ $product->id }}"
                                            data-name="{{ $product->name }}"
                                            data-category="{{ $product->category->nama_kategori ?? '-' }}"
                                            data-description="{{ $product->description }}"
                                            data-price="Rp {{ number_format($product->price, 0, ',', '.') }}"
                                            data-stock="{{ $product->stock }}"
                                            data-image="{{ $product->image ? asset('storage/products/'.$product->image) : '' }}"
                                            data-status="{{ $statusLabel }}"
                                            data-created="{{ $product->created_at->format('d M Y H:i') }}"
                                            data-updated="{{ $product->updated_at->format('d M Y H:i') }}"
                                            class="inline-flex items-center px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-md text-xs font-medium transition">
                                            🔍 Lihat Detail
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 bg-gray-50 border-t border-gray-200">
                    {{ $products->appends(request()->except('page'))->links('pagination::tailwind') }}
                </div>
            </div>
        @endif
    </div>

    <div id="detailModal" tabindex="-1" aria-hidden="true"
         class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg max-w-3xl w-full">
            <div class="flex justify-between items-center border-b p-4">
                <h5 class="text-lg font-semibold text-gray-700">📝 Detail Produk Konsinyasi</h5>
                <button type="button" class="text-gray-500 hover:text-gray-700" data-modal-hide="detailModal">&times;</button>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex justify-center">
                    <img id="detailImage" src="{{ asset('storage/no-image.png') }}" 
                         class="rounded-lg shadow-md max-h-64 object-cover" alt="Gambar Produk">
                </div>
                <div>
                    <table class="w-full text-sm text-gray-700 border border-gray-200 rounded-lg">
                        <tbody>
                            <tr><td class="p-2 font-medium w-1/3">Nama Produk</td><td class="p-2" id="detailName"></td></tr>
                            <tr><td class="p-2 font-medium">Kategori</td><td class="p-2" id="detailCategory"></td></tr>
                            <tr><td class="p-2 font-medium">Harga</td><td class="p-2" id="detailPrice"></td></tr>
                            <tr><td class="p-2 font-medium">Stok</td><td class="p-2" id="detailStock"></td></tr>
                            <tr><td class="p-2 font-medium">Status</td><td class="p-2 font-semibold" id="detailStatus"></td></tr>
                            <tr><td class="p-2 font-medium">Tanggal Diajukan</td><td class="p-2" id="detailCreated"></td></tr>
                            <tr><td class="p-2 font-medium">Tanggal Diperbarui</td><td class="p-2" id="detailUpdated"></td></tr>
                        </tbody>
                    </table>
                    <p class="mt-3 text-sm text-gray-600"><strong>Deskripsi:</strong></p>
                    <p id="detailDescription" class="text-sm text-gray-500"></p>
                </div>
            </div>

            <div class="border-t p-4 text-right flex justify-end gap-2">
                <button type="button"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-md text-sm"
                        data-modal-hide="detailModal">
                    Tutup
                </button>

                <form id="actionForm" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        id="actionButton"
                        class="bg-green-100 hover:bg-green-200 text-green-700 font-medium py-2 px-4 rounded-md text-sm">
                        🔄 Ajukan Kembali
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-modal-toggle="detailModal"]').forEach(button => {
            button.addEventListener('click', () => {
                const modal = document.getElementById('detailModal');
                modal.classList.remove('hidden');

                const img = modal.querySelector('#detailImage');
                img.src = button.dataset.image || "{{ asset('storage/no-image.png') }}";

                modal.querySelector('#detailName').innerText = button.dataset.name;
                modal.querySelector('#detailCategory').innerText = button.dataset.category;
                modal.querySelector('#detailDescription').innerText = button.dataset.description || '-';
                modal.querySelector('#detailPrice').innerText = button.dataset.price;
                modal.querySelector('#detailStock').innerText = button.dataset.stock;

                const statusEl = modal.querySelector('#detailStatus');
                const status = button.dataset.status.toLowerCase();

                statusEl.innerText = button.dataset.status;
                statusEl.classList.remove('text-yellow-700', 'text-green-700', 'text-red-700');

                if (status.includes('menunggu')) {
                    statusEl.classList.add('text-yellow-700');
                } else if (status.includes('disetujui')) {
                    statusEl.classList.add('text-green-700');
                } else {
                    statusEl.classList.add('text-red-700');
                }

                modal.querySelector('#detailCreated').innerText = button.dataset.created;
                modal.querySelector('#detailUpdated').innerText = button.dataset.updated;

                const actionForm = modal.querySelector('#actionForm');
                const actionButton = modal.querySelector('#actionButton');

                if (status.includes('menunggu')) {
                    actionForm.action = `/products/${button.dataset.id}/cancel`;
                    actionButton.innerText = '❌ Batal Pengajuan';
                    actionButton.classList.remove('bg-green-100','text-green-700','hover:bg-green-200');
                    actionButton.classList.add('bg-red-100','text-red-700','hover:bg-red-200');
                } else {
                    actionForm.action = `/products/${button.dataset.id}/resubmit`;
                    actionButton.innerText = '🔄 Ajukan Kembali';
                    actionButton.classList.remove('bg-red-100','text-red-700','hover:bg-red-200');
                    actionButton.classList.add('bg-green-100','text-green-700','hover:bg-green-200');
                }

                actionForm.onsubmit = async (e) => {
                    e.preventDefault();
                    const isCancel = status.includes('menunggu');

                    const result = await Swal.fire({
                        title: isCancel ? 'Batalkan pengajuan ini?' : 'Ajukan kembali produk ini?',
                        text: isCancel
                            ? 'Produk akan dibatalkan dari daftar pengajuan.'
                            : 'Produk akan diajukan kembali ke admin.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: isCancel ? 'Ya, batalkan' : 'Ya, ajukan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33'
                    });

                    if (result.isConfirmed) {
                        Swal.fire({
                            title: isCancel ? 'Membatalkan pengajuan...' : 'Mengajukan kembali produk...',
                            html: 'Harap tunggu sebentar.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        setTimeout(() => {
                            actionForm.submit();
                        }, 800);
                    }
                };
            });
        });

        document.querySelectorAll('[data-modal-hide="detailModal"]').forEach(button => {
            button.addEventListener('click', () => {
                document.getElementById('detailModal').classList.add('hidden');
            });
        });

    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</x-app-layout>
