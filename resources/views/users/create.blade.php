@extends('layouts.app')

@section('content')
<div class="container">
    

    {{-- Form tambah user --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Create User</h5>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                <i class="bi bi-x "></i>
            </a>
        </div>
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST" id="userCreateForm">
                    @csrf

                    {{-- Nama --}}
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text"
                            name="name"
                            id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}"
                            required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email"
                            name="email"
                            id="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                            required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password + Eye --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password"
                                name="password"
                                id="password"
                                class="form-control @error('password') is-invalid @enderror"
                                required>
                            <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                                <i class="fa fa-eye" id="eyeIcon"></i>
                            </span>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <div class="input-group">
                            <input type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                class="form-control"
                                required>
                            <span class="input-group-text" id="togglePasswordConfirm" style="cursor: pointer;">
                                <i class="fa fa-eye" id="eyeIconConfirm"></i>
                            </span>
                        </div>
                    </div>

                    {{-- Role --}}
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tombol submit --}}
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
    </div>
</div>

{{-- Script toggle password + localStorage persistence --}}
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

    const fields = ['name', 'email', 'role'];
    fields.forEach(function(field) {
        const el = document.getElementById(field);
        if (!el) return;

        const stored = localStorage.getItem('user_create_' + field);
        const isEmptySelect = el.tagName === 'SELECT' ? (el.value === '') : false;
        const isEmptyInput = el.tagName !== 'SELECT' ? (el.value === '' || el.value === null) : false;

        if (stored !== null && (isEmptySelect || isEmptyInput)) {
            el.value = stored;
        }
        const eventName = el.tagName === 'SELECT' ? 'change' : 'input';
        el.addEventListener(eventName, function () {
            try {
                localStorage.setItem('user_create_' + field, el.value);
            } catch (e) {
                console.warn('localStorage error', e);
            }
        });
    });
});
</script>
@endsection
