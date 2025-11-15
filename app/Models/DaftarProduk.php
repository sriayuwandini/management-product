<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DaftarProduk extends Model
{
    protected $table = 'daftar_produks';
    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'category_id',
        'harga',
        'deskripsi',
        'foto'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}

