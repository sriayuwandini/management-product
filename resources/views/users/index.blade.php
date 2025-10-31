<x-app-layout>

    <div class="py-8">
        <div class="px-6 lg:px-12">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg border border-green-300">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                    <h5 class="font-semibold text-gray-700">Daftar User</h5>
                    <a href="{{ route('users.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-grey-100 hover:bg-gray-800 text-black text-sm font-medium rounded-lg shadow-sm transition duration-150 ease-in-out">
                        <i class="bi bi-person-plus mr-2"></i> Tambah User
                    </a>
                </div>

                {{-- Table Wrapper --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-700">
                        <thead class="bg-gray-100 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 font-semibold uppercase text-xs tracking-wider">No</th>
                                <th class="px-6 py-3 font-semibold uppercase text-xs tracking-wider">Nama</th>
                                <th class="px-6 py-3 font-semibold uppercase text-xs tracking-wider">Email</th>
                                <th class="px-6 py-3 font-semibold uppercase text-xs tracking-wider">Role</th>
                                <th class="px-6 py-3 text-center font-semibold uppercase text-xs tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $user)
                                <tr class="border-b hover:bg-gray-50 transition">
                                    <td class="px-6 py-3">{{ $users->firstItem() + $index }}</td>
                                    <td class="px-6 py-3 font-medium text-gray-900">{{ $user->name }}</td>
                                    <td class="px-6 py-3">{{ $user->email }}</td>
                                    <td class="px-6 py-3">
                                        @php $role = $user->getRoleNames()->first(); @endphp
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $role === 'admin' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $role ? ucfirst($role) : 'Tidak Ada Role' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-center">
                                        <div class="flex justify-center space-x-2">
                                            <a href="{{ route('users.edit', $user->id) }}"
                                               class="inline-flex items-center px-3 py-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-md text-xs font-medium transition">
                                                <i class="bi bi-pencil-square mr-1"></i> Edit
                                            </a>
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                  onsubmit="return confirm('Yakin mau hapus user ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center px-3 py-1 bg-red-100 hover:bg-red-200 text-red-700 rounded-md text-xs font-medium transition">
                                                    <i class="bi bi-trash mr-1"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-6 text-center text-gray-500">
                                        Belum ada user yang terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="p-4 bg-gray-50 border-t border-gray-200">
                    {{ $users->appends(request()->except('page'))->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
