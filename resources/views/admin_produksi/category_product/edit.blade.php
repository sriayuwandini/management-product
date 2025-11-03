<x-app-layout>

    <div class="py-10">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-xl sm:rounded-lg p-6">
                <a href="{{ route('categories.index') }}"
                   class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium rounded-lg shadow-sm transition">
                    <i class="bi bi-arrow-left mr-1"></i> Kembali
                </a>

                <form action="{{ route('categories.update', $category->id) }}" method="POST" class="space-y-6 mt-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="nama_kategori" class="block text-sm font-medium text-gray-700">Nama Kategori</label>
                        <input type="text" name="nama_kategori" id="nama_kategori"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('nama_kategori') border-red-500 @enderror"
                               value="{{ old('nama_kategori', $category->nama_kategori) }}" required>
                        @error('nama_kategori')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                            <i class="bi bi-save mr-1"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>
