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
        Schema::rename('slas', 'ms_service_level_agreements');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('ms_service_level_agreements', 'slas');
    }
};
