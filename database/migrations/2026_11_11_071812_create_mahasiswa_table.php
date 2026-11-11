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
        Schema::create('mahasiswa', function (Blueprint $table) {
        $table->string('nim', 20)->primary();
        $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
        $table->string('nama_lengkap');
        $table->enum('prodi', ['TI', 'TMD', 'TMJ', 'TRK']);
        $table->string('kelas', 20);
        $table->string('no_hp', 20);
        $table->year('angkatan');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
