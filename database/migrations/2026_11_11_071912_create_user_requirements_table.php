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
        Schema::create('user_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuan_pkl')->cascadeOnDelete();
            $table->string('nama_modul_pekerjaan');
            $table->text('deskripsi_spesifikasi');
            $table->enum('status_validasi_industri', ['pending', 'valid', 'revisi'])->default('pending');
            $table->timestamp('tanggal_validasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_requirements');
    }
};
