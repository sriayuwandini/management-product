<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <div class="py-8" x-data="consignmentApp()">
        <div class="px-6 lg:px-12 max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                {{-- Alert Session --}}
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Form Tambah Produk --}}
                <div class="bg-white p-6 rounded-xl shadow space-y-4">
                    <h2 class="text-2xl font-semibold mb-4">🧾 Tambah Produk Konsinyasi</h2>

                    <div>
                        <label class="block text-gray-700 font-medium">Nama Produk</label>
                        <input type="text" x-model="form.name" class="form-control">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium">Kategori</label>
                        <select x-model="form.category" class="form-select">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium">Deskripsi</label>
                        <textarea x-model="form.description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col">
                            <label class="block text-gray-700 font-medium">Harga</label>
                            <input type="number" x-model="form.price" class="form-control">
                        </div>
                        <div class="col">
                            <label class="block text-gray-700 font-medium">Stok</label>
                            <input type="number" x-model="form.stock" class="form-control">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium">Gambar Produk</label>
                        <input type="file" id="imageUpload" @change="handleFileUpload" class="form-control" accept="image/*">
                        <small class="text-muted"
                            x-text="form.imageName ? 'File dipilih: ' + form.imageName : 'Belum ada file dipilih'"></small>
                    </div>

                    <div class="text-end pt-3">
                        <button @click="addProduct" class="btn btn-primary">+ Tambah Produk</button>
                    </div>
                </div>

                {{-- Daftar Produk --}}
                <div class="bg-white p-6 rounded-xl shadow space-y-4">
                    <h2 class="text-2xl font-semibold mb-4">📦 Daftar Produk Konsinyasi</h2>

                    <template x-if="products.length === 0">
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center text-gray-500">
                            Belum ada produk yang ditambahkan.
                        </div>
                    </template>

                    <template x-if="products.length > 0">
                        <div class="space-y-3">
                            <template x-for="(item, index) in products" :key="index">
                                <div class="bg-gray-50 p-4 rounded-lg border flex justify-between items-start shadow-sm">
                                    <div>
                                        <h4 class="font-bold text-lg" x-text="item.name"></h4>
                                        <p class="text-sm text-gray-600" x-text="'Kategori: ' + item.category"></p>
                                        <p class="text-sm text-gray-500"
                                           x-text="'Stok: ' + item.stock + ' | Harga: Rp ' + item.price"></p>
                                        <p class="text-sm text-gray-500 mt-1"
                                           x-text="'File: ' + (item.imageName || '-')"></p>

                                        <button x-show="item.image" type="button"
                                            class="text-blue-600 hover:underline text-sm mt-2"
                                            data-bs-toggle="modal" data-bs-target="#imageModal"
                                            @click="showImage(item)">
                                            📷 Lihat Foto
                                        </button>
                                    </div>

                                    <button @click="removeProduct(index)"
                                        class="text-red-600 hover:underline font-medium">
                                        ❌ Hapus
                                    </button>
                                </div>
                            </template>

                            <form id="submitForm" @submit.prevent="submitForm" enctype="multipart/form-data" class="pt-3 border-t">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 mt-3" :disabled="isSubmitting">
                                    <span x-show="!isSubmitting">✅ Kirim ke Admin Produksi</span>
                                    <span x-show="isSubmitting">⏳ Mengirim...</span>
                                </button>
                            </form>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Preview Gambar --}}
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header">
                    <h5 class="modal-title">📸 Preview Gambar Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <img id="previewImage" src="" alt="Preview" class="img-fluid rounded shadow-sm">
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Tambah Produk --}}
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
        <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ✅ Produk berhasil ditambahkan ke daftar!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    {{-- Modal Loading saat Submit --}}
    <div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-5">
                <div class="spinner-border text-success" style="width: 3rem; height: 3rem;"></div>
                <h5 class="mt-3">Mengirim data produk...</h5>
                <p class="text-muted">Harap tunggu sebentar.</p>
            </div>
        </div>
    </div>

    <script>
        function consignmentApp() {
        return {
            form: { name: '', category: '', description: '', price: '', stock: '', image: null, imageName: '' },
            products: [],
            isSubmitting: false,

            // ✅ Tambah produk ke rekapan
            async addProduct() {
                if (!this.form.name || !this.form.category || !this.form.price || !this.form.stock) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data belum lengkap!',
                        text: 'Harap isi semua field wajib sebelum menambahkan produk.',
                    });
                    return;
                }

                const result = await Swal.fire({
                    title: 'Apakah data produk sudah benar?',
                    text: 'Data akan dimasukkan ke daftar rekapan produk.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, tambahkan!'
                });

                if (result.isConfirmed) {
                    this.products.push({ ...this.form });
                    this.resetForm();

                    Swal.fire({
                        icon: 'success',
                        title: 'Produk Ditambahkan!',
                        text: 'Produk berhasil dimasukkan ke rekapan.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            },

            resetForm() {
                this.form = { name: '', category: '', description: '', price: '', stock: '', image: null, imageName: '' };
                document.getElementById('imageUpload').value = null;
            },

            removeProduct(index) {
                Swal.fire({
                    title: "Hapus Produk Ini?",
                    text: "Data produk akan dihapus dari rekapan.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya, hapus!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.products.splice(index, 1);
                        Swal.fire({
                            icon: "success",
                            title: "Dihapus!",
                            text: "Produk telah dihapus dari rekapan.",
                            timer: 1200,
                            showConfirmButton: false
                        });
                    }
                });
            },

            handleFileUpload(event) {
                const file = event.target.files[0];
                if (file) {
                    this.form.image = file;
                    this.form.imageName = file.name;
                }
            },

            showImage(item) {
                const img = document.getElementById('previewImage');
                if (item.image) {
                    img.src = URL.createObjectURL(item.image);
                }
            },

            // ✅ Ajukan ke admin dengan konfirmasi + loading + notifikasi
            async submitForm() {
                if (this.products.length === 0) {
                    Swal.fire({
                        icon: "info",
                        title: "Belum ada produk!",
                        text: "Tambahkan minimal satu produk sebelum mengirim.",
                    });
                    return;
                }

                if (this.isSubmitting) return;

                // 🔹 Konfirmasi sebelum kirim ke admin
                const confirmSubmit = await Swal.fire({
                    title: 'Ajukan ke Admin Produksi?',
                    text: 'Apakah Anda yakin semua data produk sudah benar dan siap diajukan?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, ajukan!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33'
                });

                if (!confirmSubmit.isConfirmed) return;

                this.isSubmitting = true;

                Swal.fire({
                    title: 'Mengirim data produk...',
                    html: 'Harap tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');

                this.products.forEach((item, index) => {
                    formData.append(`products[${index}][name]`, item.name);
                    formData.append(`products[${index}][category_id]`, item.category);
                    formData.append(`products[${index}][description]`, item.description);
                    formData.append(`products[${index}][price]`, item.price);
                    formData.append(`products[${index}][stock]`, item.stock);
                    if (item.image) {
                        formData.append(`products[${index}][image]`, item.image);
                    }
                });

                try {
                    const response = await fetch("{{ route('consignments.submit') }}", {
                        method: "POST",
                        body: formData
                    });

                    Swal.close();
                    this.isSubmitting = false;

                    if (!response.ok) throw new Error('Gagal mengirim data');

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Diajukan!',
                        text: 'Semua produk berhasil dikirim ke Admin Produksi.',
                        timer: 1800,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "{{ route('consignments.history') }}";
                    });

                } catch (error) {
                    Swal.close();
                    this.isSubmitting = false;
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mengirim!',
                        text: error.message,
                    });
                }
            }
        }
    }
    </script>
</x-app-layout>
