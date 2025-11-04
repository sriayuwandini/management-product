<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-8 px-6 lg:px-12">
        <div class="text-gray-500 py-2 bg-white rounded-xl shadow-md mb-4">
            <div class="p-4 flex justify-between items-center">
                <h5 class="font-semibold text-gray-700">📦 Validasi Pengajuan Konsinyasi</h5>
                <div class="space-x-2">
                    <a href="{{ route('admin.konsinyasi.riwayat.disetujui') }}" class="px-4 py-2 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg text-sm font-medium">
                        ✅ Riwayat Disetujui
                    </a>
                    <a href="{{ route('admin.konsinyasi.riwayat.ditolak') }}" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm font-medium">
                        ❌ Riwayat Ditolak
                    </a>
                </div>
            </div>
        </div>

        {{-- ================== PENGAJUAN BARU (≤ 3 HARI) ================== --}}
        <div class="alert {{ $baruUsers->count() > 0 ? 'alert-warning' : 'alert-warning' }} d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-bell-fill me-2"></i>
            <div>
                @if($baruUsers->count() > 0)
                    <strong>{{ $baruUsers->total() }}</strong> pengguna baru mengajukan produk konsinyasi dalam 3 hari terakhir.
                @else
                    Tidak ada pengajuan baru.
                @endif
            </div>
        </div>

        @forelse ($baruUsers as $user)
            @php $products = $user->products; @endphp
            <div class="bg-white shadow-md rounded-xl border mb-6">
                <div class="flex justify-between items-center p-4 border-b bg-green-50">
                    <div>
                        <h6 class="font-semibold text-gray-800">👤 {{ $user->name }}</h6>
                        <p class="text-xs text-gray-500">
                            Jumlah produk pending: {{ $products->count() }}
                        </p>
                    </div>
                    <button class="px-3 py-2 text-sm bg-blue-100 hover:bg-blue-200 rounded-lg text-blue-600 font-medium"
                        data-bs-toggle="modal"
                        data-bs-target="#modalDetail{{ $user->id }}">
                        🔍 Lihat Detail
                    </button>
                </div>
            </div>

            <div class="modal fade" id="modalDetail{{ $user->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $user->id }}" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-gray-100">
                            <h5 class="modal-title font-semibold" id="modalLabel{{ $user->id }}">
                                Detail Produk Pending — {{ $user->name }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                                            <th class="text-center">Aksi</th>
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
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <button type="button" class="btn btn-success btn-sm d-flex align-items-center justify-content-center"
                                                            onclick="confirmAction('{{ route('admin.konsinyasi.status', $product->id) }}', 'approved')"
                                                            title="Setujui" style="width: 36px; height: 36px; border-radius: 8px;">
                                                            <i class="bi bi-check-lg fs-5"></i>
                                                        </button>

                                                        <button type="button" class="btn btn-danger btn-sm d-flex align-items-center justify-content-center"
                                                            onclick="confirmAction('{{ route('admin.konsinyasi.status', $product->id) }}', 'rejected')"
                                                            title="Tolak" style="width: 36px; height: 36px; border-radius: 8px;">
                                                            <i class="bi bi-x-lg fs-5"></i>
                                                        </button>
                                                    </div>
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
        @endforelse

        @if($baruUsers->hasPages())
            <div class="mt-4 mb-6">
                {{ $baruUsers->links('pagination::tailwind') }}
            </div>
        @endif


{{-- ini pengajuan yg lama mksdnya lebih dri 3 hari --}}
        <div class="alert alert-info d-flex align-items-center mt-5 mb-4" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i>
            <div>
                @if($lamaUsers->count() > 0)
                    Pengguna berikut memiliki produk pending lebih dari 3 hari.
                @else
                    Tidak ada pengajuan yang sudah lebih dari 3 hari.
                @endif
            </div>
        </div>

        @forelse ($lamaUsers as $user)
            @php $products = $user->products; @endphp
            <div class="bg-white shadow-md rounded-xl border mb-6">
                <div class="flex justify-between items-center p-4 border-b bg-gray-50">
                    <div>
                        <h6 class="font-semibold text-gray-800">👤 {{ $user->name }}</h6>
                        <p class="text-xs text-gray-500">
                            Jumlah produk pending: {{ $products->count() }}
                        </p>
                    </div>
                    <button class="px-3 py-2 text-sm bg-blue-100 hover:bg-blue-200 rounded-lg text-blue-600 font-medium"
                        data-bs-toggle="modal"
                        data-bs-target="#modalDetail{{ $user->id }}">
                        🔍 Lihat Detail
                    </button>
                </div>
            </div>

            <div class="modal fade" id="modalDetail{{ $user->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $user->id }}" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-gray-100">
                            <h5 class="modal-title font-semibold" id="modalLabel{{ $user->id }}">
                                Detail Produk Pending — {{ $user->name }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                                            <th class="text-center">Aksi</th>
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
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <button type="button" class="btn btn-success btn-sm d-flex align-items-center justify-content-center"
                                                            onclick="confirmAction('{{ route('admin.konsinyasi.status', $product->id) }}', 'approved')"
                                                            title="Setujui" style="width: 36px; height: 36px; border-radius: 8px;">
                                                            <i class="bi bi-check-lg fs-5"></i>
                                                        </button>

                                                        <button type="button" class="btn btn-danger btn-sm d-flex align-items-center justify-content-center"
                                                            onclick="confirmAction('{{ route('admin.konsinyasi.status', $product->id) }}', 'rejected')"
                                                            title="Tolak" style="width: 36px; height: 36px; border-radius: 8px;">
                                                            <i class="bi bi-x-lg fs-5"></i>
                                                        </button>
                                                    </div>
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
        @endforelse

        @if($lamaUsers->hasPages())
            <div class="mt-4">
                {{ $lamaUsers->links('pagination::tailwind') }}
            </div>
        @endif
    </div>

    <script>
        function confirmAction(url, status) {
            const actionText = status === 'approved' ? 'menyetujui' : 'menolak';
            Swal.fire({
                title: `Apakah Anda yakin ingin ${actionText} produk ini?`,
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Ya, lanjutkan",
                cancelButtonText: "Batal",
                confirmButtonColor: status === 'approved' ? '#198754' : '#d33',
                cancelButtonColor: '#6c757d',
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = `
                        @csrf
                        <input type="hidden" name="status" value="${status}">
                    `;
                    document.body.appendChild(form);
                    form.submit();

                    Swal.fire({
                        title: "Memproses...",
                        text: "Mohon tunggu sebentar",
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading(),
                    });
                }
            });
        }

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 1800,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
            });
        @endif
    </script>
</x-app-layout>
