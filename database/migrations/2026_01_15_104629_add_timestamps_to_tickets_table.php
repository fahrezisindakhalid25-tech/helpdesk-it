<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('tickets', function (Blueprint $table) {
        // Kita tambah 3 kolom waktu baru (Boleh kosong/nullable)
        $table->timestamp('replied_at')->nullable();
        $table->timestamp('solved_at')->nullable();
        $table->timestamp('closed_at')->nullable();
    });
}

public function down(): void
{
    Schema::table('tickets', function (Blueprint $table) {
        $table->dropColumn(['replied_at', 'solved_at', 'closed_at']);
    });
}
};
