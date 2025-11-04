{{-- <x-app-layout>
    <div class="py-10 px-6 lg:px-12">

        <!-- Salam dan Jam Digital -->
        <div class="bg-white shadow-md rounded-xl p-6 flex flex-col md:flex-row justify-between items-center mb-6">
            <div>
                <h2 id="greeting" class="text-2xl font-bold text-gray-800"></h2>
                <p class="text-gray-500 mt-1">Semoga harimu menyenangkan, Admin!</p>
            </div>
            <div class="text-right mt-4 md:mt-0">
                <h3 id="clock" class="text-3xl font-mono text-indigo-600 font-semibold"></h3>
            </div>
        </div>

        <!-- Info Profil Singkat -->
        <div class="bg-white shadow-md rounded-xl p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">👤 Informasi Akun</h3>
            <p class="text-gray-700"><strong>Nama:</strong> {{ Auth::user()->name }}</p>
            <p class="text-gray-700"><strong>Email:</strong> {{ Auth::user()->email }}</p>
            <p class="text-gray-700"><strong>Role:</strong> {{ ucfirst(Auth::user()->getRoleNames()->first() ?? 'Admin Produksi') }}</p>
            <div class="mt-4">
                <a href="#"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                    <i class="bi bi-person-fill me-2"></i> Lihat Profil
                </a>
            </div>
        </div>

        <!-- Statistik Cards -->
        @php
            $today = \Carbon\Carbon::today();
            $totalToday = \App\Models\Product::whereDate('created_at', $today)->where('status', 'pending')->count();
            $totalPending = \App\Models\Product::where('status', 'pending')->count();
            $totalApproved = \App\Models\Product::where('status', 'approved')->count();
            $totalRejected = \App\Models\Product::where('status', 'rejected')->count();
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <div class="bg-blue-100 border border-blue-200 rounded-xl p-5 flex items-center justify-between shadow">
                <div>
                    <p class="text-sm text-gray-600">Pengajuan Hari Ini</p>
                    <h3 class="text-2xl font-bold text-blue-700 mt-1">{{ $totalToday }}</h3>
                </div>
                <div class="text-blue-500 text-3xl"><i class="bi bi-calendar-plus"></i></div>
            </div>

            <div class="bg-yellow-100 border border-yellow-200 rounded-xl p-5 flex items-center justify-between shadow">
                <div>
                    <p class="text-sm text-gray-600">Menunggu Validasi</p>
                    <h3 class="text-2xl font-bold text-yellow-700 mt-1">{{ $totalPending }}</h3>
                </div>
                <div class="text-yellow-500 text-3xl"><i class="bi bi-hourglass-split"></i></div>
            </div>

            <div class="bg-green-100 border border-green-200 rounded-xl p-5 flex items-center justify-between shadow">
                <div>
                    <p class="text-sm text-gray-600">Telah Disetujui</p>
                    <h3 class="text-2xl font-bold text-green-700 mt-1">{{ $totalApproved }}</h3>
                </div>
                <div class="text-green-500 text-3xl"><i class="bi bi-check-circle"></i></div>
            </div>

            <div class="bg-red-100 border border-red-200 rounded-xl p-5 flex items-center justify-between shadow">
                <div>
                    <p class="text-sm text-gray-600">Telah Ditolak</p>
                    <h3 class="text-2xl font-bold text-red-700 mt-1">{{ $totalRejected }}</h3>
                </div>
                <div class="text-red-500 text-3xl"><i class="bi bi-x-circle"></i></div>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 mt-4">
            <div class="p-4 border-b bg-gray-50 border-gray-200 flex justify-between items-center">
                <h5 class="font-semibold text-gray-700">🕓 Pengajuan Terbaru</h5>
                <a href="{{ route('admin.konsinyasi.index') }}" class="text-sm text-blue-600 hover:underline">Lihat Semua</a>
            </div>

            @php
                $recentSubmissions = \App\Models\Product::with('category', 'user')
                    ->where('status', 'pending')
                    ->latest()
                    ->take(3)
                    ->get();
            @endphp

            @if($recentSubmissions->isEmpty())
                <div class="text-center text-gray-500 py-8">
                    Belum ada pengajuan baru hari ini.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-700">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-6 py-3">Nama Produk</th>
                                <th class="px-6 py-3">Diajukan Oleh</th>
                                <th class="px-6 py-3">Kategori</th>
                                <th class="px-6 py-3">Harga</th>
                                <th class="px-6 py-3">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentSubmissions as $product)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-3">{{ $product->name }}</td>
                                    <td class="px-6 py-3">{{ $product->user->name ?? '-' }}</td>
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

                document.getElementById("greeting").textContent = `${greet}, Admin {{ Auth::user()->name }} 👋`;
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