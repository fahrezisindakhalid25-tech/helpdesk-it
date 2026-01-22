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
        // Kita buat nullable dulu karena data lama belum punya UUID
        $table->uuid('uuid')->after('id')->nullable()->unique();
    });
}

public function down(): void
{
    Schema::table('tickets', function (Blueprint $table) {
        $table->dropColumn('uuid');
    });
}
};
