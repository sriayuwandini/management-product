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
                <h5 class="font-semibold text-gray-700">Daftar Kategori Produk</h5>
                <a href="{{ route('categories.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-black text-sm font-medium rounded-lg shadow-sm transition duration-150 ease-in-out">
                    <i class="bi bi-plus-lg mr-2"></i> Tambah Kategori
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700">
                    <thead class="bg-gray-100 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 font-semibold uppercase text-xs tracking-wider">No</th>
                            <th class="px-6 py-3 font-semibold uppercase text-xs tracking-wider">Nama Kategori</th>
                            <th class="px-6 py-3 text-center font-semibold uppercase text-xs tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $index => $kategori)
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="px-6 py-3">{{ $categories->firstItem() + $index }}</td>
                                <td class="px-6 py-3 font-medium text-gray-900">{{ $kategori->nama_kategori }}</td>
                                <td class="px-6 py-3 text-center">
                                    <div class="flex justify-center space-x-2">
                                        <a href="{{ route('categories.edit', $kategori->id) }}"
                                           class="inline-flex items-center px-3 py-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-md text-xs font-medium transition">
                                            <i class="bi bi-pencil-square mr-1"></i> Edit
                                        </a>

                                        <form action="{{ route('categories.destroy', $kategori->id) }}" method="POST" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                    class="btn-delete inline-flex items-center px-3 py-1 bg-red-100 hover:bg-red-200 text-red-700 rounded-md text-xs font-medium transition"
                                                    data-name="{{ $kategori->nama_kategori }}">
                                                <i class="bi bi-trash mr-1"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-6 text-center text-gray-500">
                                    Belum ada kategori produk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-gray-50 border-t border-gray-200">
                {{ $categories->appends(request()->except('page'))->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', async (e) => {
                    const form = button.closest('.delete-form');
                    const name = button.dataset.name;

                    const result = await Swal.fire({
                        title: 'Yakin ingin menghapus?',
                        text: `Kategori "${name}" akan dihapus secara permanen.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6'
                    });

                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menghapus...',
                            text: 'Harap tunggu sebentar.',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });

                        setTimeout(() => {
                            form.submit();
                        }, 700);
                    }
                });
            });
        });
    </script>
</x-app-layout>
