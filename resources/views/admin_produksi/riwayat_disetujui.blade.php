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
                <div class="flex justify-between items-center p-4 border-b bg-gray-50">
                    <div>
                        <h6 class="font-semibold text-gray-800">👤 {{ $user->name }}</h6>
                        <p class="text-xs text-gray-500">
                            Jumlah produk: {{ $products->count() }}
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
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>

                        <div class="modal-body">
                            <div class="overflow-x-auto">
                                <table class="table table-bordered align-middle text-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Produk</th>
                                            <th>Kategori</th>
                                            <th>Harga</th>
                                            <th>Stok</th>
                                            <th>Deskripsi</th>
                                            <th>Foto</th>
                                            <th>Diajukan</th>
                                            <th>Disetujui</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($products as $product)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $product->name }}</td>
                                                <td>{{ $product->category->nama_kategori ?? '-' }}</td>
                                                <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                                <td>{{ $product->stock }}</td>
                                                <td>{{ Str::limit($product->description, 60) }}</td>
                                                <td class="text-center">
                                                    @if ($product->image && file_exists(storage_path('app/public/' . $product->image)))
                                                        <button class="border rounded-lg bg-gray-100 hover:bg-gray-200 p-1"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#fotoModal{{ $product->id }}">
                                                            <img src="{{ asset('storage/' . $product->image) }}"
                                                                alt="Foto Produk"
                                                                class="w-16 h-16 object-cover rounded">
                                                        </button>
                                                    @else
                                                        <span class="text-gray-400 text-xs">Tidak ada</span>
                                                    @endif
                                                </td>
                                                <td>{{ $product->created_at->format('d M Y H:i') }}</td>
                                                <td>{{ $product->updated_at->format('d M Y H:i') }}</td>
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
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

            @foreach ($products as $product)
                @if ($product->image && file_exists(storage_path('app/public/' . $product->image)))
                    <div class="modal fade" id="fotoModal{{ $product->id }}" tabindex="-1" aria-labelledby="fotoLabel{{ $product->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content bg-transparent border-0 shadow-none">
                                <div class="modal-body text-center position-relative">
                                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                                    <img src="{{ asset('storage/' . $product->image) }}"
                                         class="img-fluid rounded shadow-lg"
                                         alt="Foto Produk">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        @empty
            <div class="bg-white shadow-md rounded-xl border p-6 text-center text-gray-500">
                Belum ada produk yang disetujui.
            </div>
        @endforelse

        <div class="mt-4">
            {{ $users->links('pagination::tailwind') }}
        </div>
    </div>
</x-app-layout>
