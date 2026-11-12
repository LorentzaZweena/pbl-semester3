<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logbook_harian', function (Blueprint $table) {
            if (!Schema::hasColumn('logbook_harian', 'nip')) {
                $table->string('nip', 30)->nullable()->after('pengajuan_id');
            }
            $table->foreign('nip')->references('nip')->on('dosen')->onDelete('set null');

            if (!Schema::hasColumn('logbook_harian', 'status_persetujuan_dosen')) {
                $table->enum('status_persetujuan_dosen', ['menunggu', 'disetujui', 'revisi'])->default('menunggu')->after('status_validasi');
            }
            if (!Schema::hasColumn('logbook_harian', 'ttd_dosen')) {
                $table->string('ttd_dosen')->nullable()->after('status_persetujuan_dosen');
            }
        });
    }

    public function down(): void
    {
        Schema::table('logbook_harian', function (Blueprint $table) {
            $table->dropForeign(['nip']);
            $table->dropColumn(['nip', 'status_persetujuan_dosen', 'ttd_dosen']);
        });
    }
};