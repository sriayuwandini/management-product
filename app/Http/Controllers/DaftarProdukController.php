<?php

namespace App\Http\Controllers;

use App\Models\DaftarProduk;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DaftarProdukController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $produks = DaftarProduk::with('category')->latest()->paginate(10);

        return view('owner.produk.index', compact('produks', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('owner.produk.create', compact('categories'));
    }

    private function generateKodeProduk($kategoriNama, $produkNama)
    {
        $kategoriKode = strtoupper(substr($kategoriNama, 0, 3));

        $words = explode(' ', $produkNama);
        $inisial = '';
        foreach ($words as $w) {
            $inisial .= strtoupper(substr($w, 0, 1));
            if (strlen($inisial) >= 2) break;
        }

        $lastProduct = DaftarProduk::whereHas('category', function($q) use ($kategoriNama) {
            $q->where('nama_kategori', $kategoriNama);
        })->orderBy('id', 'desc')->first();

        $nextNumber = 1;
        if ($lastProduct && $lastProduct->kode_produk) {
            $parts = explode('-', $lastProduct->kode_produk);
            if (isset($parts[1])) {
                $nextNumber = intval($parts[1]) + 1;
            }
        }

        $nomor = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return "{$kategoriKode}-{$nomor}-{$inisial}";
    }


    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $kategori = Category::findOrFail($request->category_id);

        $kodeProduk = $this->generateKodeProduk($kategori->nama_kategori, $request->nama_produk);

        $data = $request->all();
        $data['kode_produk'] = $kodeProduk;

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('produk', 'public');
        }

        DaftarProduk::create($data);

        return redirect()->route('owner.produk.index')
            ->with('success', '✅ Produk berhasil ditambahkan!');
    }


    public function edit(DaftarProduk $produk)
    {
        $categories = Category::all();
        return view('owner.produk.edit', compact('produk', 'categories'));
    }


    public function update(Request $request, DaftarProduk $produk)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $kategori = Category::findOrFail($request->category_id);

        if ($request->nama_produk !== $produk->nama_produk || 
            $request->category_id != $produk->category_id) 
        {
            $produk->kode_produk = $this->generateKodeProduk(
                $kategori->nama_kategori,
                $request->nama_produk
            );
        }

        $produk->nama_produk = $request->nama_produk;
        $produk->category_id = $request->category_id;
        $produk->harga = $request->harga;
        $produk->deskripsi = $request->deskripsi;

        if ($request->hasFile('foto')) {
            if ($produk->foto && Storage::disk('public')->exists($produk->foto)) {
                Storage::disk('public')->delete($produk->foto);
            }
            $produk->foto = $request->file('foto')->store('produk', 'public');
        }

        $produk->save();

        return redirect()->route('owner.produk.index')
            ->with('success', '✅ Produk berhasil diperbarui!');
    }


    public function destroy(DaftarProduk $produk)
    {
        if ($produk->foto && Storage::disk('public')->exists($produk->foto)) {
            Storage::disk('public')->delete($produk->foto);
        }

        $produk->delete();

        return redirect()->route('owner.produk.index')
            ->with('success', '🗑️ Produk berhasil dihapus!');
    }
}
