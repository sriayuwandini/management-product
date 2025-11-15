<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Sidebar */
        .sidebar {
            width: 240px;
            background-color: #1f2937;
            color: white;
            min-height: 100vh;
            position: fixed;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .sidebar a {
            display: block;
            padding: 0.75rem 1rem;
            color: white;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: background 0.2s ease;
        }
        .sidebar a:hover,
        .sidebar a.active {
            background-color: #2563eb;
        }

        /* Main */
        .main-content {
            margin-left: 240px;
            padding: 2rem;
        }
        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                flex-direction: column;
            }
            .main-content {
                margin-left: 0;
            }
        }

        .dropdown {
            position: relative;
        }
        .dropdown-menu {
            display: none;
            position: absolute;
            left: 0;
            bottom: 45px;
            background: #1f2937 !important; 
            border-radius: 0.5rem;
            overflow: hidden;
            width: 200px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 50;
        }

        .dropdown-menu a,
        .dropdown-menu button {
            display: block;
            width: 100%;
            text-align: left;
            padding: 0.75rem 1rem;
            color: #fff !important;
            background: transparent !important;
            border: none;
            cursor: pointer;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .dropdown-menu a:hover,
        .dropdown-menu button:hover,
        .dropdown-menu a.active {
            background: #2563eb !important; /* biru */
            color: #fff !important;
        }

        .dropdown.open .dropdown-menu {
            display: block;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-100">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="sidebar p-4">
            <div>
                <div class="mb-6 text-center">
                    <h2 class="text-xl font-bold text-white">Manajemen Produk</h2>
                    <hr class="my-3 border-gray-600">
                </div>

                @php
                    use Illuminate\Support\Facades\Auth;

                    if (Auth::user()->hasRole('owner')) {
                        $dashboardUrl = url('/owner/dashboard');
                    } elseif (Auth::user()->hasRole('user')) {
                        $dashboardUrl = url('/user/dashboard');
                    } elseif (Auth::user()->hasRole('admin_produksi')) {
                        $dashboardUrl = url('/admin/produksi/dashboard');
                    } elseif (Auth::user()->hasRole('admin_penjualan')) {
                        $dashboardUrl = url('/admin/penjualan/dashboard');
                    } elseif (Auth::user()->hasRole('sales')) {
                        $dashboardUrl = url('/sales/dashboard');
                    } else {
                        $dashboardUrl = url('/dashboard');
                    }
                @endphp

                <nav class="space-y-2">
                    <a href="{{ $dashboardUrl }}">🏠 Dashboard</a>

                    {{-- OWNER dan ADMIN PENJUALAN --}}
                    @if(Auth::user()->hasRole('admin_penjualan') || Auth::user()->hasRole('owner'))
                        <a href="{{ route('users.index') }}">👥 Users</a>
                        <a href="{{ route('owner.produk.index') }}">👥 Produk</a>
                        <a href="{{ route('categories.index') }}">📂 Kategori</a>
                    @endif

                    {{-- ADMIN PRODUKSI --}}
                    @if(Auth::user()->hasRole('admin_produksi'))
                        <a href="{{ route('admin.konsinyasi.index') }}">🧾 Validasi</a>
                        <a href="{{ route('categories.index') }}">📂 Kategori</a>
                    @endif

                    {{-- USER --}}
                    @if(Auth::user()->hasRole('user'))
                        <a href="{{ route('consignments.create') }}">📦 Ajukan</a>
                        <a href="{{ route('consignments.history') }}">📜 Riwayat</a>
                    @endif

                    {{-- SALES --}}
                    @if(Auth::user()->hasRole('sales'))
                        <a href="{{ route('sales.index') }}">🛍️ Produk</a>
                        <a href="{{ route('sales.reports.index') }}">📊 Laporan Penjualan</a>
                    @endif
                </nav>
            </div>

            <!-- User Dropdown -->
            <div class="mt-auto pt-6 border-t border-gray-700">
                <div class="dropdown" id="roleDropdown">
                    <button type="button"
                            class="w-full flex justify-between items-center px-3 py-2 bg-gray-700 rounded hover:bg-gray-600">
                        <span>{{ Auth::user()->name }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div class="dropdown-menu mt-2">
                        <a href="{{ route('profile.edit') }}" 
                           class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">🧑‍💼 Profile</a>
                        <a href="#">⚙️ Setting</a>

                        <!-- Logout -->
                        <form id="logoutForm" method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="button" id="logoutButton">🚪 Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main-content flex-1">
            @isset($header)
                <header class="bg-white shadow mb-6 rounded-lg">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Dropdown toggle
            const dropdown = document.getElementById("roleDropdown");
            if (dropdown) {
                dropdown.querySelector("button").addEventListener("click", () => {
                    dropdown.classList.toggle("open");
                });
            }

            // SweetAlert2 Logout
            const logoutButton = document.getElementById('logoutButton');
            const logoutForm = document.getElementById('logoutForm');

            logoutButton.addEventListener('click', () => {
                Swal.fire({
                    title: 'Apakah kamu yakin?',
                    text: 'Apakah kamu serius ingin logout?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, logout',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Keluar...',
                            text: 'Sedang memproses logout.',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });

                        setTimeout(() => {
                            logoutForm.submit();
                        }, 800);
                    }
                });
            });
        });
    </script>
</body>
</html>
