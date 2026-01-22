<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slas', function (Blueprint $table) {
            // Ubah tipe kolom dari Integer ke String agar bisa simpan teks
            // Kita gunakan change()
            $table->string('response_days')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('slas', function (Blueprint $table) {
            // Kembalikan ke Integer jika di-rollback (hati-hati data teks bisa hilang/error)
            $table->integer('response_days')->nullable()->default(0)->change();
        });
    }
};