<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Kita tambahkan kolom sla_id
            // nullable() artinya boleh kosong dulu (untuk tiket lama)
            $table->foreignId('sla_id')
                  ->nullable()
                  ->after('topik_bantuan') // Kita taruh setelah kolom kategori
                  ->constrained('slas')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['sla_id']);
            $table->dropColumn('sla_id');
        });
    }
};