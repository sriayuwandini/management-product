<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User; 

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = User::find(Auth::id());

        if (!$user) {
            Auth::logout();
            return redirect('/login')->withErrors(['email' => 'User tidak ditemukan.']);
        }

        if ($user->hasRole('owner')) return redirect()->intended('/dashboard');
        if ($user->hasRole('user')) return redirect()->intended('/user/dashboard');
        if ($user->hasRole('admin_produksi')) return redirect()->intended('/admin/produksi/dashboard');
        if ($user->hasRole('admin_penjualan')) return redirect()->intended('/admin/penjualan/dashboard');
        if ($user->hasRole('sales')) return redirect()->intended('/sales');


        return redirect('/')->withErrors('role not found');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
