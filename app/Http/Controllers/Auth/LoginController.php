<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        if ($request->filled('role') && $user->role !== $request->role) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withInput($request->only('email', 'role'))
                ->withErrors(['role' => 'Akun ini tidak terdaftar dengan peran yang dipilih.']);
        }

        return match ($user->role) {
            'admin_prodi'         => redirect('/admin-prodi/dashboard'),
            'mahasiswa'           => redirect('/mahasiswa/dashboard'),
            'dosen'               => redirect('/dosen/dashboard'),
            'pembimbing_industri' => redirect('/industri/dashboard'),
            'kps'                 => redirect('/kps/dashboard'),
            'super_admin'         => redirect('/super-admin/dashboard'),
            default               => redirect($this->redirectTo),
        };
    }
}