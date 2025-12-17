<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesDetail extends Model
{
    protected $fillable = [
        'sale_id', 
        'product_id', 
        'quantity_order',
        'quantity_delivery', 
        'quantity_sold', 
        'price', 
        'subtotal'
    ];

    public function sale()
    {
        return $this->belongsTo(Sales::class);
    }

    public function produk()
    {
        return $this->belongsTo(DaftarProduk::class, 'product_id');
    }
}
