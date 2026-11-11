<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengajuan_pkl', function (Blueprint $table) {
            $table->id();
            $table->string('nim', 20);
            $table->foreign('nim')->references('nim')->on('mahasiswa')->cascadeOnDelete();
            $table->foreignId('instansi_id')->constrained('instansi_mitra');
            $table->foreignId('pembimbing_industri_id')->nullable()->constrained('pembimbing_industri');
            
            $table->string('opsi_dosbing_1', 30);
            $table->string('opsi_dosbing_2', 30);
            $table->string('opsi_dosbing_3', 30);
            $table->string('dosbing_terpilih', 30)->nullable();
            
            $table->foreign('opsi_dosbing_1')->references('nip')->on('dosen');
            $table->foreign('opsi_dosbing_2')->references('nip')->on('dosen');
            $table->foreign('opsi_dosbing_3')->references('nip')->on('dosen');
            $table->foreign('dosbing_terpilih')->references('nip')->on('dosen');

            $table->string('file_bukti_keterimaan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('status_verifikasi', ['pending', 'revisi', 'disetujui_admin', 'ditolak', 'aktif_pkl'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_pkl');
    }
};
