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
        Schema::create('logbook_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuan_pkl')->cascadeOnDelete();
            $table->date('tanggal');
            $table->integer('minggu_ke');
            $table->text('uraian_kegiatan');
            $table->string('dokumentasi_foto')->nullable();
            $table->enum('status_validasi', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('catatan_pembimbing')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbook_harian');
    }
};
