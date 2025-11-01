<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Makanan',
            'Minuman',
            'Pakaian',
            'Aksesoris',
            'Elektronik',
            'Peralatan Rumah Tangga',
            'Kesehatan & Kecantikan',
            'Kerajinan Tangan',
            'Lain-lain',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['nama_kategori' => $name]);
        }
    }
}