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
        Schema::table('ms_service_level_agreements', function (Blueprint $table) {
            $table->dropColumn(['name', 'number', 'response_days', 'response_time', 'resolution_days']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ms_service_level_agreements', function (Blueprint $table) {
            $table->after('id', function (Blueprint $table) {
                $table->string('name');
                $table->string('number')->nullable();

                $table->string('response_days')->nullable();
                $table->time('response_time')->nullable();
                $table->integer('resolution_days')->default(0);
            });
        });
    }
};
