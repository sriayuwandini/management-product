<x-app-layout>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Tambah User</h3>
                        <a href="{{ route('users.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            <i class="bi bi-x me-1"></i> Kembali
                        </a>
                    </div>

                    <form action="{{ route('users.store') }}" method="POST" id="userCreateForm" class="space-y-4">
                        @csrf

                        <div>
                            <x-input-label for="name" value="Nama" />
                            <x-text-input id="name" name="name" type="text"
                                class="mt-1 block w-full"
                                value="{{ old('name') }}"
                                required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" value="Email" />
                            <x-text-input id="email" name="email" type="email"
                                class="mt-1 block w-full"
                                value="{{ old('email') }}"
                                required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password" value="Password" />
                            <div class="relative">
                                <x-text-input id="password" name="password" type="password"
                                    class="mt-1 block w-full pr-10"
                                    required />
                                <span id="togglePassword"
                                      class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-gray-500">
                                    <i class="fa fa-eye" id="eyeIcon"></i>
                                </span>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                            <div class="relative">
                                <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                                    class="mt-1 block w-full pr-10"
                                    required />
                                <span id="togglePasswordConfirm"
                                      class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-gray-500">
                                    <i class="fa fa-eye" id="eyeIconConfirm"></i>
                                </span>
                            </div>
                        </div>

                        <div>
                            <x-input-label for="role" value="Role" />
                            <select id="role" name="role"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Pilih Role --</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <div class="flex justify-end">
                            <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function setupToggle(inputId, toggleId, iconId) {
                const toggle = document.getElementById(toggleId);
                const input = document.getElementById(inputId);
                const icon = document.getElementById(iconId);
                if (!toggle || !input || !icon) return;
                toggle.addEventListener("click", () => {
                    const type = input.getAttribute("type") === "password" ? "text" : "password";
                    input.setAttribute("type", type);
                    icon.classList.toggle("fa-eye");
                    icon.classList.toggle("fa-eye-slash");
                });
            }
            setupToggle("password", "togglePassword", "eyeIcon");
            setupToggle("password_confirmation", "togglePasswordConfirm", "eyeIconConfirm");
        });
    </script>
</x-app-layout>
