<x-app-layout>
    <div class="py-8 px-6 lg:px-12">

        @if(session('success'))
            <script>
                document.addEventListener("DOMContentLoaded", () => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: '{{ session('success') }}',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                });
            </script>
        @endif

        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                <h5 class="font-semibold text-gray-700">Daftar Produk</h5>

                <button type="button"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-black text-sm font-medium rounded-lg shadow-sm transition"
                        data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="bi bi-plus-lg mr-2"></i> Tambah Produk
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700">
                    <thead class="bg-gray-100 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 font-semibold uppercase text-xs">No</th>
                            <th class="px-6 py-3 font-semibold uppercase text-xs">Kode Produk</th>
                            <th class="px-6 py-3 font-semibold uppercase text-xs">Nama Produk</th>
                            <th class="px-6 py-3 font-semibold uppercase text-xs">Kategori</th>
                            <th class="px-6 py-3 font-semibold uppercase text-xs">Harga</th>
                            <th class="px-6 py-3 font-semibold uppercase text-xs">Foto</th>
                            <th class="px-6 py-3 text-center font-semibold uppercase text-xs">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse($produks as $index => $produk)
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="px-6 py-3">{{ $index + 1 }}</td>

                                <td class="px-6 py-3 font-semibold text-indigo-600">
                                    {{ $produk->kode_produk ?? '-' }}
                                </td>

                                <td class="px-6 py-3 font-medium text-gray-900">
                                    {{ $produk->nama_produk }}
                                </td>

                                <td class="px-6 py-3">
                                    {{ $produk->category->nama_kategori ?? '-' }}
                                </td>

                                <td class="px-6 py-3">
                                    Rp {{ number_format($produk->harga, 0, ',', '.') }}
                                </td>

                                <td class="px-6 py-3">
                                    @if ($produk->foto && file_exists(storage_path('app/public/' . $produk->foto)))
                                        <button class="border rounded-lg bg-gray-100 hover:bg-gray-200 p-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#fotoModal{{ $produk->id }}">
                                            <img src="{{ asset('storage/' . $produk->foto) }}"
                                                 class="w-16 h-16 object-cover rounded">
                                        </button>
                                    @else
                                        <span class="text-gray-400 text-xs">Tidak ada</span>
                                    @endif
                                </td>

                                <td class="px-6 py-3 text-center">
                                    <div class="flex justify-center space-x-2">

                                        <button type="button"
                                            class="inline-flex items-center px-3 py-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded-md text-xs"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal{{ $produk->id }}">
                                            <i class="bi bi-pencil-square mr-1"></i> Edit
                                        </button>

                                        <form action="{{ route('owner.produk.destroy', $produk->id) }}"
                                              method="POST"
                                              class="delete-form">
                                            @csrf
                                            @method('DELETE')

                                            <button type="button"
                                                class="btn-delete px-3 py-1 bg-red-100 hover:bg-red-200 text-red-700 rounded-md text-xs"
                                                data-name="{{ $produk->nama_produk }}">
                                                <i class="bi bi-trash mr-1"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <div class="modal fade" id="editModal{{ $produk->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="{{ route('owner.produk.update', $produk->id) }}"
                                              method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Produk</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body space-y-3">

                                                <div>
                                                    <label class="text-sm font-medium">Kode Produk</label>
                                                    <input type="text" readonly
                                                        value="{{ $produk->kode_produk }}"
                                                        class="mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 text-sm">
                                                </div>

                                                <div>
                                                    <label class="text-sm font-medium">Nama Produk</label>
                                                    <input type="text" name="nama_produk"
                                                        value="{{ $produk->nama_produk }}"
                                                        class="mt-1 block w-full rounded-lg border-gray-300 text-sm" required>
                                                </div>

                                                <div>
                                                    <label class="text-sm font-medium">Kategori</label>
                                                    <select name="category_id" class="mt-1 block w-full rounded-lg border-gray-300 text-sm" required>
                                                        @foreach($categories as $cat)
                                                            <option value="{{ $cat->id }}"
                                                                {{ $produk->category_id == $cat->id ? 'selected' : '' }}>
                                                                {{ $cat->nama_kategori }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="text-sm font-medium">Harga</label>
                                                    <input type="number" name="harga"
                                                        value="{{ $produk->harga }}"
                                                        class="mt-1 block w-full rounded-lg border-gray-300 text-sm" required>
                                                </div>

                                                <div>
                                                    <label class="text-sm font-medium">Deskripsi</label>
                                                    <textarea name="deskripsi" rows="3"
                                                        class="mt-1 block w-full rounded-lg border-gray-300 text-sm">{{ $produk->deskripsi }}</textarea>
                                                </div>

                                                <div>
                                                    <label class="text-sm font-medium">Foto Produk</label>
                                                    <input type="file" name="foto"
                                                        class="mt-1 block w-full text-sm">

                                                    @if($produk->foto)
                                                        <p class="text-xs text-gray-500 mt-1">Foto saat ini:</p>
                                                        <img src="{{ asset('storage/' . $produk->foto) }}"
                                                             class="w-16 h-16 object-cover rounded-lg mt-1">
                                                    @endif
                                                </div>

                                            </div>

                                            <div class="modal-footer">
                                                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button class="btn btn-primary">Simpan</button>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-6 text-center text-gray-500">
                                    Belum ada produk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4 bg-gray-50 border-t border-gray-200">
                    {{ $produks->appends(request()->except('page'))->links('pagination::tailwind') }}
                </div>


                @foreach($produks as $produk)
                    @if ($produk->foto && file_exists(storage_path("app/public/$produk->foto")))
                        <div class="modal fade" id="fotoModal{{ $produk->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Foto: {{ $produk->nama_produk }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <img src="{{ asset("storage/$produk->foto") }}" class="img-fluid rounded">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

            </div>
        </div>
    </div>

    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('owner.produk.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Produk</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body space-y-3">

                        <div>
                            <label class="text-sm font-medium">Kode Produk (Auto)</label>
                            <input type="text" class="mt-1 w-full rounded-lg bg-gray-100 border-gray-300 text-sm" readonly
                                   placeholder="Otomatis setelah disimpan">
                        </div>
                        <div>
                            <label class="text-sm font-medium">Nama Produk</label>
                            <input type="text" name="nama_produk"
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm" required>
                        </div>

                        <div>
                            <label class="text-sm font-medium">Kategori</label>
                            <select name="category_id"
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-sm font-medium">Harga</label>
                            <input type="number" name="harga"
                                   class="mt-1 block w-full rounded-lg border-gray-300 text-sm" required>
                        </div>

                        <div>
                            <label class="text-sm font-medium">Deskripsi</label>
                            <textarea name="deskripsi" rows="3"
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm"></textarea>
                        </div>

                        <div>
                            <label class="text-sm font-medium">Foto Produk</label>
                            <input type="file" name="foto" class="mt-1 block w-full text-sm">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
    document.addEventListener("DOMContentLoaded", () => {

    document.querySelectorAll("form").forEach(form => {

        form.addEventListener("submit", function (e) {
            const requiredFields = form.querySelectorAll("[required]");

            for (let field of requiredFields) {
                if (!field.value.trim()) {
                    e.preventDefault();

                    Swal.fire({
                        icon: "warning",
                        title: "Data belum lengkap!",
                        text: "Harap isi semua field wajib sebelum menyimpan.",
                    });

                    return false;
                }
            }

            Swal.fire({
                title: "Menyimpan...",
                text: "Harap tunggu sebentar.",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading()
            });
        });
    });


    document.querySelectorAll(".btn-delete").forEach(button => {

        button.addEventListener("click", function (e) {
            e.preventDefault();

            let form = this.closest("form");
            let nama = this.dataset.name;

            Swal.fire({
                title: "Hapus Produk?",
                text: "Produk '" + nama + "' akan dihapus secara permanen.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hapus",
                cancelButtonText: "Batal",
            }).then(result => {

                if (result.isConfirmed) {

                    Swal.fire({
                        title: "Menghapus...",
                        text: "Harap tunggu sebentar.",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => Swal.showLoading()
                    });

                    setTimeout(() => {
                        form.submit();
                    }, 700);
                }
            });
        });
    });

});
</script>
</x-app-layout>
