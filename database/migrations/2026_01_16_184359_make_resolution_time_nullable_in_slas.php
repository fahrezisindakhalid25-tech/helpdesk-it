<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slas', function (Blueprint $table) {
            // Ubah agar boleh kosong (Nullable)
            // Kita gunakan change() untuk memodifikasi kolom yang sudah ada
            $table->time('resolution_time')->nullable()->change();
            
            // Sekalian resolution_days juga biar aman
            if (Schema::hasColumn('slas', 'resolution_days')) {
                $table->integer('resolution_days')->nullable()->default(0)->change();
            }
        });
    }

    public function down(): void
    {
        // Kembalikan ke pengaturan awal (Tidak Boleh Kosong)
        Schema::table('slas', function (Blueprint $table) {
            $table->time('resolution_time')->nullable(false)->change();
        });
    }
};