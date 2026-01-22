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
            $table->dropColumn('gambar');
        });
        
        Schema::table('tickets', function (Blueprint $table) {
            $table->json('gambar')->nullable()->after('penjelasan_lengkap');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('gambar');
        });
        
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('gambar')->nullable()->after('penjelasan_lengkap');
        });
    }
};
