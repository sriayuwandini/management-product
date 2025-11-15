<x-app-layout>
    <div class="py-8 px-6 lg:px-12">
        <div class="text-gray-500 py-2 bg-white rounded-xl shadow-md mb-4">
            <div class="p-4 flex justify-between items-center">
                <h5 class="font-semibold text-gray-700">✅ Riwayat Pengajuan Disetujui</h5>
                <a href="{{ route('admin.konsinyasi.index') }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">
                   Kembali ke Validasi
                </a>
            </div>
        </div>

        @forelse ($users as $user)
            @php
                $products = $user->products->where('status', 'approved');
            @endphp
            <div class="bg-white shadow-md rounded-xl border mb-6">
                <div class="flex justify-between items-center p-4 border-b bg-green-50">
                    <div>
                        <h6 class="font-semibold text-gray-800">👤 {{ $user->name }}</h6>
                        <p class="text-xs text-gray-500">
                            Jumlah produk disetujui: {{ $products->count() }}
                        </p>
                    </div>
                    <button class="px-3 py-2 text-sm bg-green-100 hover:bg-green-200 rounded-lg text-green-700 font-medium"
                        data-bs-toggle="modal"
                        data-bs-target="#modalDisetujui{{ $user->id }}">
                        🔍 Lihat Detail
                    </button>
                </div>
            </div>

            <div class="modal fade" id="modalDisetujui{{ $user->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $user->id }}" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-gray-100">
                            <h5 class="modal-title font-semibold" id="modalLabel{{ $user->id }}">
                                Detail Produk Disetujui — {{ $user->name }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="overflow-x-auto">
                                <table class="table table-bordered align-middle text-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Produk</th>
                                            <th>Nama Produk</th>
                                            <th>Kategori</th>
                                            <th>Harga</th>
                                            <th>Stok</th>
                                            <th>Deskripsi</th>
                                            <th>Foto</th>
                                            <th>Diajukan</th>
                                            <th>Tanggal Ditolak</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($products as $product)
                                            @php $dp = $product->daftarProduk; @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $dp->kode_produk ?? '-' }}</td>
                                                <td>{{ $dp->nama_produk ?? '-' }}</td>
                                                <td>{{ $dp->category->nama_kategori ?? '-' }}</td>
                                                <td>Rp {{ number_format($dp->harga ?? 0, 0, ',', '.') }}</td>
                                                <td>{{ $product->stock }}</td>
                                                <td>{{ Str::limit($dp->deskripsi ?? '-', 60) }}</td>
                                                <td class="text-center">
                                                    @if ($dp->foto && file_exists(storage_path('app/public/' . $dp->foto)))
                                                        <img src="{{ asset('storage/' . $dp->foto) }}" 
                                                             alt="Foto Produk" 
                                                             class="w-14 h-14 object-cover rounded mx-auto shadow">
                                                    @else
                                                        <span class="text-gray-400 text-xs">Tidak ada</span>
                                                    @endif
                                                </td>
                                                <td>{{ $product->created_at->format('d M Y H:i') }}</td>
                                                <td>{{ $product->updated_at->format('d M Y H:i') }}</td>
                                                <td class="text-center">
                                                    <button 
                                                        onclick="toggleRiwayat('{{ $product->id }}', this)"
                                                        class="px-3 py-1 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg text-xs font-medium">
                                                        📦 Lihat Riwayat
                                                    </button>
                                                </td>
                                            </tr>

                                            <tr id="riwayat-{{ $product->id }}" class="hidden bg-gray-50">
                                                <td colspan="10" class="p-3">
                                                    @php
                                                        $logs = \App\Models\StockLog::where('product_id', $product->id)
                                                            ->orderBy('created_at', 'desc')
                                                            ->get();
                                                    @endphp
                                                    <h6 class="font-semibold text-gray-700 mb-2">
                                                        📜 Riwayat Stok — {{ $dp->nama_produk ?? '-' }}
                                                    </h6>
                                                    @if ($logs->isEmpty())
                                                        <p class="text-gray-500 text-sm italic">Belum ada riwayat stok.</p>
                                                    @else
                                                        <div class="overflow-x-auto">
                                                            <table class="table table-sm text-xs w-full border">
                                                                <thead class="bg-gray-100 text-gray-600">
                                                                    <tr>
                                                                        <th class="px-2 py-1">#</th>
                                                                        <th class="px-2 py-1">Tanggal</th>
                                                                        <th class="px-2 py-1">Jumlah</th>
                                                                        <th class="px-2 py-1">Tipe</th>
                                                                        <th class="px-2 py-1">Keterangan</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($logs as $log)
                                                                        <tr>
                                                                            <td class="px-2 py-1">{{ $loop->iteration }}</td>
                                                                            <td class="px-2 py-1">{{ $log->created_at->format('d M Y H:i') }}</td>
                                                                            <td class="px-2 py-1">{{ $log->quantity }}</td>
                                                                            <td class="px-2 py-1 capitalize">
                                                                                @switch($log->type)
                                                                                    @case('addition') Penambahan @break
                                                                                    @case('reduction') Pengurangan @break
                                                                                    @case('submit') Pengajuan Awal @break
                                                                                    @case('resubmit') Pengajuan Ulang @break
                                                                                    @case('cancel') Pembatalan @break
                                                                                    @case('approve_new') Persetujuan Awal @break
                                                                                    @case('approve_stock') Persetujuan Tambahan @break
                                                                                    @default {{ $log->type }}
                                                                                @endswitch
                                                                            </td>
                                                                            <td class="px-2 py-1 text-gray-600">{{ $log->description }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="modal-footer justify-content-between">
                            <div>
                                <span class="text-muted small">Total Produk: {{ $products->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white shadow-md rounded-xl border p-6 text-center text-gray-500">
                Belum ada produk yang disetujui.
            </div>
        @endforelse

        <div class="mt-4">
            {{ $users->links('pagination::tailwind') }}
        </div>
    </div>

    <script>
        function toggleRiwayat(id, btn) {
            const row = document.getElementById('riwayat-' + id);
            const isHidden = row.classList.contains('hidden');

            document.querySelectorAll('[id^="riwayat-"]').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('button[data-open="true"]').forEach(b => {
                b.innerText = '📦 Lihat Riwayat';
                b.dataset.open = "false";
            });

            if (isHidden) {
                row.classList.remove('hidden');
                btn.innerText = '⬆️ Tutup Riwayat';
                btn.dataset.open = "true";
            }
        }
    </script>
</x-app-layout>
