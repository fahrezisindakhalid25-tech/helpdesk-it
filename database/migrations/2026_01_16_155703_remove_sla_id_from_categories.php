<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // 1. Hapus Kunci Asing (Foreign Key) dulu biar gak error
            // Kita pakai try-catch atau pengecekan biar aman kalau key-nya beda nama,
            // tapi cara standar Laravel biasanya cukup begini:
            $table->dropForeign(['sla_id']); 
            
            // 2. Baru hapus kolomnya
            $table->dropColumn('sla_id');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Kalau dibatalkan, kembalikan kolomnya
            $table->foreignId('sla_id')->constrained('slas')->onDelete('cascade');
        });
    }
};