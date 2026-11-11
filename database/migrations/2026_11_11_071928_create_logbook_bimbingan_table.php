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
        Schema::create('logbook_bimbingan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuan_pkl')->cascadeOnDelete();
            $table->enum('tipe_pembimbing', ['kampus', 'industri']);
            $table->date('tanggal_bimbingan');
            $table->text('topik_bahasan');
            $table->text('catatan_revisi')->nullable();
            $table->enum('status_validasi', ['menunggu', 'disetujui'])->default('menunggu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbook_bimbingan');
    }
};
