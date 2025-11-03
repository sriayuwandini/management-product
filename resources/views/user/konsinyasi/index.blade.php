<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <div class="py-8" x-data="consignmentApp()">
        <div class="px-6 lg:px-12 max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

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
                                <button type="submit" class="btn btn-success w-100 mt-3">
                                    ✅ Kirim ke Admin Produksi
                                </button>
                            </form>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

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

    <script>
        function consignmentApp() {
            return {
                form: { name: '', category: '', description: '', price: '', stock: '', image: null, imageName: '' },
                products: [],

                addProduct() {
                    if (!this.form.name || !this.form.category || !this.form.price || !this.form.stock) {
                        alert('Harap isi semua field wajib!');
                        return;
                    }
                    this.products.push({ ...this.form });
                    this.resetForm();
                },

                resetForm() {
                    this.form = { name: '', category: '', description: '', price: '', stock: '', image: null, imageName: '' };
                    document.getElementById('imageUpload').value = null;
                },

                removeProduct(index) {
                    if (confirm('Yakin ingin menghapus produk ini?')) {
                        this.products.splice(index, 1);
                    }
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

                async submitForm() {
                    if (this.products.length === 0) {
                        alert('Belum ada produk yang ditambahkan!');
                        return;
                    }

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

                        if (!response.ok) throw new Error('Gagal mengirim data');

                        alert('✅ Produk berhasil diajukan!');
                        window.location.href = "{{ route('consignments.history') }}";
                    } catch (error) {
                        alert('❌ Terjadi kesalahan: ' + error.message);
                    }
                }
            }
        }
    </script>
</x-app-layout>
