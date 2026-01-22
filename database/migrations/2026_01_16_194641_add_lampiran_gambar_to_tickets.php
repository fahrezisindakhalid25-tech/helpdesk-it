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
        Schema::table('tickets', function (Blueprint $table) {
            // Kita taruh kolom gambar setelah kolom penjelasan
            $table->string('lampiran_gambar')->nullable()->after('penjelasan_lengkap');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('lampiran_gambar');
        });
    }
};
