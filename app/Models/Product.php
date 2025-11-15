<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
protected $fillable = [
    'user_id',
    'daftar_produks_id',
    'stock',
    'status',
];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sales()
    {
        return $this->hasMany(Sales::class);
    }

    public function stockLogs()
    {
        return $this->hasMany(StockLog::class);
    }

    public function latestStockLog()
    {
        return $this->hasOne(\App\Models\StockLog::class)->latestOfMany();
    }

    public function daftarProduk()
    {
        return $this->belongsTo(DaftarProduk::class, 'daftar_produks_id');
    }

}
