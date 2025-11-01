<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
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
        }
        .sidebar a:hover,
        .sidebar a.active {
            background-color: #2563eb;
        }
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
            background: #374151;
            border-radius: 0.5rem;
            overflow: hidden;
            width: 200px;
        }
        .dropdown-menu a,
        .dropdown-menu button {
            display: block;
            width: 100%;
            text-align: left;
            padding: 0.75rem 1rem;
            color: #fff;
            background: transparent;
            border: none;
            cursor: pointer;
        }
        .dropdown-menu a:hover,
        .dropdown-menu button:hover {
            background: #2563eb;
        }
        .dropdown.open .dropdown-menu {
            display: block;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-100">
    <div class="flex">

        <aside class="sidebar p-4">
            <div>
                <div class="mb-6 text-center">
                    <h2 class="text-xl font-bold text-white">Manajemen Produk</h2>

                    <hr class="my-3 border-gray-600">
                </div>

                <nav class="space-y-2">
                    <a href="{{ route('dashboard') }}">🏠 Dashboard</a>
                    <a href="{{ route('users.index') }}">👥 Users</a>
                </nav>
            </div>

            <div class="mt-auto pt-6 border-t border-gray-700">
                @php
                    $roleName = ucfirst(Auth::user()->getRoleNames()->first() ?? 'User');
                @endphp

                <div class="dropdown" id="roleDropdown">
                    <button type="button" class="w-full flex justify-between items-center px-3 py-2 bg-gray-700 rounded hover:bg-gray-600">
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
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">🚪 Logout</button>
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

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const dropdown = document.getElementById("roleDropdown");
            if (dropdown) {
                dropdown.querySelector("button").addEventListener("click", () => {
                    dropdown.classList.toggle("open");
                });
            }
        });
    </script>
</body>
</html>
