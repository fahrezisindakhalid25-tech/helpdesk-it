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
        Schema::rename('locations', 'ms_locations');

        Schema::table('ms_locations', function (Blueprint $table) {
            $table->string('name', 80)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('ms_locations', 'locations');

        Schema::table('locations', function (Blueprint $table) {
            $table->string('name')->change();
        });
    }
};
