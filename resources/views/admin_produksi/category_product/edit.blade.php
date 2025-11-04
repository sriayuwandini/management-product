<x-app-layout>
    <div class="py-10">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
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

            <div class="bg-white shadow-xl sm:rounded-lg p-6">
                <a href="{{ route('categories.index') }}"
                   class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium rounded-lg shadow-sm transition">
                    <i class="bi bi-arrow-left mr-1"></i> Kembali
                </a>

                <form id="updateForm" action="{{ route('categories.update', $category->id) }}" method="POST" class="space-y-6 mt-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="nama_kategori" class="block text-sm font-medium text-gray-700">Nama Kategori</label>
                        <input type="text" name="nama_kategori" id="nama_kategori"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('nama_kategori') border-red-500 @enderror"
                               value="{{ old('nama_kategori', $category->nama_kategori) }}" required>
                        @error('nama_kategori')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                            <i class="bi bi-save mr-1"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const form = document.getElementById('updateForm');

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const name = document.getElementById('nama_kategori').value.trim();

                const result = await Swal.fire({
                    title: 'Simpan Perubahan?',
                    text: `Kategori akan diperbarui menjadi "${name}".`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, simpan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33'
                });

                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menyimpan...',
                        html: 'Harap tunggu sebentar.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    setTimeout(() => {
                        form.submit();
                    }, 800);
                }
            });
        });
    </script>
</x-app-layout>
