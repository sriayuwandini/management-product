<x-app-layout>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Tambah Kategori Produk</h3>
                        <a href="{{ route('categories.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            <i class="bi bi-x me-1"></i> Kembali
                        </a>
                    </div>

                    <form id="tambahForm" action="{{ route('categories.store') }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <x-input-label for="nama_kategori" value="Nama Kategori" />
                            <x-text-input id="nama_kategori" name="nama_kategori" type="text"
                                class="mt-1 block w-full"
                                value="{{ old('nama_kategori') }}"
                                required />
                            <x-input-error :messages="$errors->get('nama_kategori')" class="mt-2" />
                        </div>

                        <div class="flex justify-end">
                            <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const form = document.getElementById('tambahForm');

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const name = document.getElementById('nama_kategori').value.trim();

                const result = await Swal.fire({
                    title: 'Simpan Kategori?',
                    text: `Kategori baru dengan nama "${name}" akan ditambahkan.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, tambah',
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
