<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Product Coming Soon</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-800 flex flex-col min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white shadow-md p-4 flex justify-between items-center">
        <h1 class="text-xl font-semibold text-indigo-600">Manajemen Produk</h1>
        <div class="flex items-center space-x-4">
            <span class="text-gray-600">Hi, {{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg transition">
                    Logout
                </button>
            </form>
        </div>
    </nav>

    <!-- Konten utama di tengah layar -->
    <main class="flex-grow flex items-center justify-center">
        <div class="text-center p-8 bg-white shadow-lg rounded-2xl w-11/12 sm:w-2/3 md:w-1/2 lg:w-1/3">
            <h2 class="text-3xl font-bold text-gray-800 mb-3">🛍️ Product Page</h2>
            <p class="text-gray-500 mb-6">Our product section is currently under development.</p>
            <span class="inline-block bg-indigo-600 text-white px-5 py-2 rounded-full font-semibold tracking-wide">
                Coming Soon
            </span>
        </div>
    </main>

    <!-- Footer nempel di bawah -->
    <footer class="text-center py-4 text-gray-500 text-sm border-t bg-white mt-auto">
        &copy; {{ date('Y') }} MyApp. All rights reserved.
    </footer>

</body>
</html>
