<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\InstansiMitra;
use App\Models\Mahasiswa;
use App\Models\PembimbingIndustri;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password123');

        User::create([
            'email' => 'superadmin@pnj.ac.id',
            'password' => $password,
            'role' => 'super_admin',
        ]);

        User::create([
            'email' => 'admin.ti@pnj.ac.id',
            'password' => $password,
            'role' => 'admin_prodi',
        ]);

        $userKps = User::create([
            'email' => 'kps.ti@pnj.ac.id',
            'password' => $password,
            'role' => 'kps',
        ]);

        Dosen::create([
            'nip' => '198501012010121001',
            'user_id' => $userKps->id,
            'nama_lengkap' => 'Dr. Budi Santoso, M.Kom.',
            'prodi_homebase' => 'TI',
            'kuota_maksimal' => 10,
        ]);

        for ($i = 1; $i <= 3; $i++) {
            $userDosen = User::create([
                'email' => "dosen{$i}@pnj.ac.id",
                'password' => $password,
                'role' => 'dosen',
            ]);

            Dosen::create([
                'nip' => "19900{$i}01201504100{$i}",
                'user_id' => $userDosen->id,
                'nama_lengkap' => "Dosen Pembimbing {$i}, M.T.",
                'prodi_homebase' => 'TI',
                'kuota_maksimal' => 8,
            ]);
        }

        $userMhs = User::create([
            'email' => 'mahasiswa@stu.pnj.ac.id',
            'password' => $password,
            'role' => 'mahasiswa',
        ]);

        Mahasiswa::create([
            'nim' => '2307411001',
            'user_id' => $userMhs->id,
            'nama_lengkap' => 'Andi Pratama',
            'prodi' => 'TI',
            'kelas' => 'TI-5A',
            'no_hp' => '081234567890',
            'angkatan' => 2023,
        ]);

        $instansi = InstansiMitra::create([
            'nama_perusahaan' => 'PT Teknologi Nusantara Tbk',
            'alamat' => 'Jl. Sudirman Kav. 10, Jakarta',
            'bidang_industri' => 'Software Development',
            'kontak_hrd' => 'hrd@teknusa.co.id',
        ]);

        $userIndustri = User::create([
            'email' => 'mentor@teknusa.co.id',
            'password' => $password,
            'role' => 'pembimbing_industri',
        ]);
        
        PembimbingIndustri::create([
            'user_id' => $userIndustri->id,
            'instansi_id' => $instansi->id,
            'nama_lengkap' => 'Rina Wijaya, S.Kom.',
            'jabatan' => 'Senior Tech Lead',
            'no_hp' => '089876543210',
        ]);
    }
}