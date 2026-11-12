<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penilaian_pkl', function (Blueprint $table) {
            if (!Schema::hasColumn('penilaian_pkl', 'nip')) {
                $table->string('nip', 30)->nullable()->after('pengajuan_id');
            }
            $table->foreign('nip')->references('nip')->on('dosen')->onDelete('set null');

            if (!Schema::hasColumn('penilaian_pkl', 'pembimbing_industri_id')) {
                $table->unsignedBigInteger('pembimbing_industri_id')->nullable()->after('nip');
            }
            $table->foreign('pembimbing_industri_id')->references('id')->on('pembimbing_industri')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('penilaian_pkl', function (Blueprint $table) {
            $table->dropForeign(['nip']);
            $table->dropForeign(['pembimbing_industri_id']);
            $table->dropColumn(['nip', 'pembimbing_industri_id']);
        });
    }
};