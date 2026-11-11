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
        Schema::create('penilaian_pkl', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->unique()->constrained('pengajuan_pkl')->cascadeOnDelete();
            $table->decimal('skor_softskill', 5, 2)->nullable();
            $table->decimal('skor_hardskill', 5, 2)->nullable();
            $table->decimal('rata_rata_industri', 5, 2)->nullable();
            $table->decimal('skor_bimbingan', 5, 2)->nullable();
            $table->decimal('skor_laporan', 5, 2)->nullable();
            $table->decimal('skor_presentasi', 5, 2)->nullable();
            $table->decimal('rata_rata_kampus', 5, 2)->nullable();
            $table->decimal('nilai_akhir_angka', 5, 2)->nullable();
            $table->string('nilai_akhir_huruf', 2)->nullable();
            $table->enum('status_kelulusan', ['lulus', 'tidak_lulus', 'belum_lengkap'])->default('belum_lengkap');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_pkl');
    }
};
