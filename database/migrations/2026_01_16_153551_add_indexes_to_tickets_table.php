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
        $table->index('uuid');        // Biar buka detail ngebut
        $table->index('no_tiket');    // Biar search no tiket ngebut
        $table->index('created_at');  // Biar sorting tanggal ngebut
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            //
        });
    }
};
