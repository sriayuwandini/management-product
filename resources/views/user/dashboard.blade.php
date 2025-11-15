{{-- @section('content')
<x-app-layout>
    <div class="py-10 px-6 lg:px-12">

        <div class="bg-white shadow-md rounded-xl p-6 flex flex-col md:flex-row justify-between items-center mb-6">
            <div>
                <h2 id="greeting" class="text-2xl font-bold text-gray-800"></h2>
                <p class="text-gray-500 mt-1">Semoga harimu menyenangkan!</p>
            </div>
            <div class="text-right mt-4 md:mt-0">
                <h3 id="clock" class="text-3xl font-mono text-indigo-600 font-semibold"></h3>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-xl p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">👤 Informasi Akun</h3>
            <p class="text-gray-700"><strong>Nama:</strong> {{ Auth::user()->name }}</p>
            <p class="text-gray-700"><strong>Email:</strong> {{ Auth::user()->email }}</p>
            <p class="text-gray-700"><strong>Role:</strong> {{ ucfirst(Auth::user()->getRoleNames()->first() ?? 'User') }}</p>
            <div class="mt-4">
                <a href="#" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                    <i class="bi bi-person-fill me-2"></i> Lihat Profil
                </a>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 mb-8">
            <div class="p-4 border-b bg-yellow-50 border-gray-200 flex justify-between items-center">
                <h5 class="font-semibold text-gray-700">📦 Barang Sedang Diajukan</h5>
                <a href="{{ route('consignments.history') }}" class="text-sm text-blue-600 hover:underline">Lihat Semua</a>
            </div>

            @php
                $pendingProducts = \App\Models\Product::where('user_id', Auth::id())
                    ->where('status', 'pending')
                    ->latest()
                    ->take(3)
                    ->get();
            @endphp

            @if($pendingProducts->isEmpty())
                <div class="text-center text-gray-500 py-6">
                    Tidak ada barang yang sedang diajukan.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-700">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-6 py-3">Nama Produk</th>
                                <th class="px-6 py-3">Kategori</th>
                                <th class="px-6 py-3">Harga</th>
                                <th class="px-6 py-3">Diajukan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingProducts as $product)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-3">{{ $product->name }}</td>
                                    <td class="px-6 py-3">{{ $product->category->nama_kategori ?? '-' }}</td>
                                    <td class="px-6 py-3">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td class="px-6 py-3 text-gray-600">{{ $product->created_at->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
            <div class="p-4 border-b bg-blue-50 border-gray-200 flex justify-between items-center">
                <h5 class="font-semibold text-gray-700">📜 Riwayat Pengajuan</h5>
                <a href="{{ route('consignments.history') }}" class="text-sm text-blue-600 hover:underline">Lihat Semua</a>
            </div>

            @php
                $historyProducts = \App\Models\Product::where('user_id', Auth::id())
                    ->whereIn('status', ['approved', 'rejected'])
                    ->latest()
                    ->take(3)
                    ->get();
            @endphp

            @if($historyProducts->isEmpty())
                <div class="text-center text-gray-500 py-6">
                    Belum ada riwayat pengajuan.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-700">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-6 py-3">Nama Produk</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Diperbarui</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historyProducts as $product)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-3">{{ $product->name }}</td>
                                    <td class="px-6 py-3">
                                        @if($product->status == 'approved')
                                            <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full">Disetujui</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-700 rounded-full">Ditolak</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-gray-600">{{ $product->updated_at->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            function updateClock() {
                const now = new Date();
                const hours = now.getHours().toString().padStart(2, '0');
                const minutes = now.getMinutes().toString().padStart(2, '0');
                const seconds = now.getSeconds().toString().padStart(2, '0');
                document.getElementById("clock").textContent = `${hours}:${minutes}:${seconds}`;
            }

            function greeting() {
                const hour = new Date().getHours();
                let greet = "Selamat datang";

                if (hour < 11) greet = "Selamat pagi";
                else if (hour < 15) greet = "Selamat siang";
                else if (hour < 18) greet = "Selamat sore";
                else greet = "Selamat malam";

                document.getElementById("greeting").textContent = `${greet}, {{ Auth::user()->name }} 👋`;
            }

            greeting();
            updateClock();
            setInterval(updateClock, 1000);
        });
    </script>
</x-app-layout> --}}

@section('content')
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>