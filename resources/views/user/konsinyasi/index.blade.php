<x-app-layout>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>[x-cloak] { display: none !important; }</style>

    @php
        $produkJson = $daftarProduks->map(function($p){
            return [
                'id' => $p->id,
                'kode_produk' => $p->kode_produk,
                'nama_produk' => $p->nama_produk,
                'harga' => $p->harga,
                'deskripsi' => $p->deskripsi,
                'stok' => $p->stock ?? 1,
                'category' => $p->category ? [
                    'id' => $p->category->id,
                    'nama_kategori' => $p->category->nama_kategori
                ] : null,
                'foto' => $p->foto ? asset('storage/'.$p->foto) : null
            ];
        })->values();
    @endphp

    <div x-data="konsinyasiApp()" x-init="init()" x-cloak class="py-8 px-6 lg:px-12">
        <div class="max-w-6xl mx-auto space-y-6">
            <h1 class="text-2xl font-semibold text-gray-800 mb-4">Pengajuan Produk Konsinyasi</h1>

            <div class="bg-white rounded-2xl shadow-sm border p-6">
                <input type="text" x-model="q" @input.debounce.200ms="filterList()"
                       placeholder="Cari nama atau kode produk..."
                       class="w-full border rounded-lg px-4 py-2 shadow-sm mb-2 focus:outline-none focus:ring-2 focus:ring-indigo-200">

                <div class="text-sm text-gray-500 mb-4" x-show="q">
                    Hasil pencarian untuk "<span x-text="q"></span>": <span x-text="filtered.length"></span> produk ditemukan
                </div>

                <div class="overflow-x-auto">
                    <table class="table-auto w-full text-sm border-collapse border">
                        <thead class="bg-indigo-50">
                            <tr>
                                <th class="p-2 w-12 text-center">No</th>
                                <th class="p-2 text-center">Kode Produk</th>
                                <th class="p-2 text-center">Nama Produk</th>
                                <th class="p-2 w-40 text-center">Kategori</th>
                                <th class="p-2 w-32 text-center">Harga</th>
                                <th class="p-2 text-center">Deskripsi</th>
                                <th class="p-2 w-24 text-center">Foto</th>
                                <th class="p-2 w-32 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <template x-for="(p, index) in paginated" :key="p.id">
                                <tr>
                                    <td class="p-2 text-center" x-text="index + 1 + (currentPage-1)*perPage"></td>
                                    <td class="p-2 font-medium text-gray-800" x-text="p.kode_produk"></td>
                                    <td class="p-2 font-medium text-gray-800" x-text="p.nama_produk"></td>
                                    <td class="p-2 text-gray-600" x-text="p.category?.nama_kategori ?? '-'"></td>
                                    <td class="p-2 text-indigo-600 font-semibold" x-text="formatRupiah(p.harga)"></td>
                                    <td class="p-2 text-gray-500" x-text="p.deskripsi ? (p.deskripsi.length>60 ? p.deskripsi.substring(0,60)+'...' : p.deskripsi) : '-'"></td>
                                    <td class="p-2 text-center">
                                        <template x-if="p.foto">
                                            <img :src="p.foto" class="w-16 h-16 object-cover rounded mx-auto">
                                        </template>
                                        <template x-if="!p.foto">
                                            <span class="text-xs text-gray-400">Tidak ada</span>
                                        </template>
                                    </td>
                                    <td class="p-2 flex justify-center gap-2">
                                        <button @click="addSingleToRekap(p)" class="text-green-600 hover:text-green-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>

                            <template x-if="filtered.length===0">
                                <tr>
                                    <td colspan="8" class="p-6 text-center text-gray-500">Tidak ada produk.</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="p-4 bg-gray-50 border-t border-gray-200">
                    {{ $daftarProduks->appends(request()->except('page'))->links('pagination::tailwind') }}
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border p-6">
                <h3 class="text-lg font-medium text-gray-700 mb-3">Rekapan Produk</h3>
                <div class="text-sm text-gray-500 mb-3">Produk yang akan diajukan ke Admin Produksi</div>

                <template x-if="rekap.length===0">
                    <div class="text-gray-500 text-sm">Belum ada produk di rekap.</div>
                </template>

                <template x-if="rekap.length>0">
                    <div class="overflow-x-auto max-h-96">
                        <table class="table-auto w-full text-sm border-collapse border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="p-2 w-12 text-center">No</th>
                                    <th class="p-2">Kode Produk</th>
                                    <th class="p-2">Nama Produk</th>
                                    <th class="p-2 w-40">Kategori</th>
                                    <th class="p-2 w-32">Harga</th>
                                    <th class="p-2">Jumlah Diajukan</th>
                                    <th class="p-2 w-32 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <template x-for="(r,index) in rekap" :key="r.id">
                                    <tr>
                                        <td class="p-2 text-center" x-text="index+1"></td>
                                        <td class="p-2 text-gray-800" x-text="r.kode_produk"></td>
                                        <td class="p-2 font-medium text-gray-800" x-text="r.nama_produk"></td>
                                        <td class="p-2 text-gray-600" x-text="r.category?.nama_kategori ?? '-'"></td>
                                        <td class="p-2 text-indigo-600 font-semibold" x-text="formatRupiah(r.harga)"></td>
                                        <td class="hidden" x-text="r.stok"></td>
                                        <td class="p-2">
                                            <input type="number" min="1" x-model.number="r.jumlah" class="w-20 border rounded px-2 py-1 text-sm">
                                        </td>
                                        <td class="p-2 flex justify-center gap-2">
                                            <button @click="removeRekap(index)" 
                                                    class="px-2 py-1 bg-red-500 text-white rounded text-sm" 
                                                    :data-name="r.nama_produk">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <form action="{{ route('consignments.submit') }}" method="POST" class="mt-2" @submit.prevent="confirmSubmit">
                            @csrf
                            <div x-html="hiddenInputsHtml"></div>
                            <div class="flex gap-3 mt-3">
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Kirim</button>
                                <button type="button" @click="clearRekap()" class="px-4 py-2 border rounded-lg text-sm">Bersihkan Rekap</button>
                            </div>
                        </form>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <script>
        function konsinyasiApp() {
            const produk = @json($produkJson);
            return {
                allProducts: produk,
                q: '',
                filtered: [],
                rekap: [],
                hiddenInputsHtml: '',
                modalDetail: false,
                detail: {},
                currentPage: 1,
                perPage: 10,

                init() { 
                    this.filtered = this.allProducts; 
                    this.modalDetail = false;
                },

                get totalPages() { return Math.ceil(this.filtered.length / this.perPage); },
                get paginated() {
                    const start = (this.currentPage-1)*this.perPage;
                    return this.filtered.slice(start, start+this.perPage);
                },

                filterList() {
                    const q = (this.q||'').trim().toLowerCase();
                    if(!q){ this.filtered=this.allProducts; this.currentPage=1; return; }
                    this.filtered=this.allProducts.filter(p =>
                        (p.nama_produk && p.nama_produk.toLowerCase().includes(q)) ||
                        (p.kode_produk && p.kode_produk.toLowerCase().includes(q))
                    );
                    this.currentPage=1;
                },

                openDetail(p){ this.detail=p; this.modalDetail=true; },

                addSingleToRekap(item){
                    if(!this.rekap.find(r=>r.id===item.id)){
                        let clone=JSON.parse(JSON.stringify(item));
                        clone.jumlah=1;
                        this.rekap.push(clone);
                        Swal.fire({ icon:'success', title:'Ditambahkan', text:'Produk masuk ke rekap.', timer:1000, showConfirmButton:false});
                    } else {
                        Swal.fire({ icon:'info', title:'Sudah ada', text:'Produk sudah ada di rekap.', timer:900, showConfirmButton:false});
                    }
                },

                removeRekap(i){
                    let name = this.rekap[i].nama_produk;
                    Swal.fire({
                        title: 'Hapus Produk?',
                        text: "Produk '"+name+"' akan dihapus dari rekap.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal'
                    }).then(result=>{
                        if(result.isConfirmed){
                            this.rekap.splice(i,1);
                            Swal.fire({icon:'success', title:'Dihapus', timer:800, showConfirmButton:false});
                        }
                    });
                },

                clearRekap(){ this.rekap=[]; this.hiddenInputsHtml=''; },

                confirmSubmit(e){
                    if(this.rekap.length===0){ 
                        Swal.fire({ icon:'warning', title:'Belum ada produk', text:'Tambahkan produk ke rekap terlebih dahulu.'}); 
                        return;
                    }

                    Swal.fire({
                        title: 'Mengirim Produk...',
                        html: 'Mohon tunggu sebentar...',
                        didOpen: () => { Swal.showLoading() },
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false
                    });

                    let html='';
                    this.rekap.forEach((p,i)=>{
                        html += `<input type="hidden" name="products[${i}][daftar_produks_id]" value="${p.id}">`;
                        html += `<input type="hidden" name="products[${i}][stock]" value="${p.jumlah}">`;
                    });
                    this.hiddenInputsHtml = html;

                    setTimeout(() => {
                        e.target.submit();
                    }, 500); 
                },

                formatRupiah(v){ if(v===null||v===undefined) return '-'; return 'Rp ' + Number(v).toLocaleString('id-ID'); }
            };
        }
    </script>
</x-app-layout>
