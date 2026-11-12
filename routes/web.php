<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes(['register' => false]);

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'role:super_admin'])->prefix('super-admin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', fn() => view('dashboard.superadmin'))->name('dashboard');
});

Route::middleware(['auth', 'role:admin_prodi'])->prefix('admin-prodi')->name('adminprodi.')->group(function () {
    Route::get('/dashboard', fn() => view('dashboard.adminprodi'))->name('dashboard');
});

Route::middleware(['auth', 'role:kps'])->prefix('kps')->name('kps.')->group(function () {
    Route::get('/dashboard', fn() => view('dashboard.kps'))->name('dashboard');
});

Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', fn() => view('dashboard.mahasiswa'))->name('dashboard');
});

Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
    Route::get('/dashboard', fn() => view('dashboard.dosen'))->name('dashboard');
});

Route::middleware(['auth', 'role:pembimbing_industri'])->prefix('industri')->name('industri.')->group(function () {
    Route::get('/dashboard', fn() => view('dashboard.industri'))->name('dashboard');
});