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
        'foto',
        'user_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function sales()
    {
        return $this->hasMany(Sales::class);
    }

    public function salesDetails()
    {
        return $this->hasMany(SalesDetail::class, 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function stockLogs()
    {
        return $this->hasMany(StockLog::class, 'product_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
 
}

