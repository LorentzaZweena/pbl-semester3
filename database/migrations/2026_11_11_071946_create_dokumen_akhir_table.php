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
        Schema::create('dokumen_akhir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->unique()->constrained('pengajuan_pkl')->cascadeOnDelete();
            $table->string('file_form_f1')->nullable();
            $table->string('file_form_f7')->nullable();
            $table->string('file_laporan_akhir_pdf')->nullable();
            $table->string('file_sertifikat_industri')->nullable();
            $table->string('link_video_demo')->nullable();
            $table->boolean('status_sinkronisasi_repo')->default(false);
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_akhir');
    }
};
