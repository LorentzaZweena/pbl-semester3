<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $role = Auth::user()->role;

        return match ($role) {
            'super_admin'         => redirect()->route('superadmin.dashboard'),
            'admin_prodi'         => redirect()->route('adminprodi.dashboard'),
            'kps'                 => redirect()->route('kps.dashboard'),
            'mahasiswa'           => redirect()->route('mahasiswa.dashboard'),
            'dosen'               => redirect()->route('dosen.dashboard'),
            'pembimbing_industri' => redirect()->route('industri.dashboard'),
            default               => abort(403, 'Role tidak dikenali.'),
        };
    }
}
