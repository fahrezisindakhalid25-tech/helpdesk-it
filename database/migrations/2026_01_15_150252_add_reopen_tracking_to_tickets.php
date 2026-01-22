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
        $table->timestamp('reopened_at')->nullable();      // Waktu user balas chat (Re-Open)
        $table->timestamp('last_reply_at')->nullable();    // Waktu admin balas chat (Reply Terakhir)
    });
}

public function down(): void
{
    Schema::table('tickets', function (Blueprint $table) {
        $table->dropColumn(['reopened_at', 'last_reply_at']);
    });
}
};
