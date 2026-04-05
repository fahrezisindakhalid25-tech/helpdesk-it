<?php

use Filament\Schemas\Components\Tabs\Tab;
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
            $table->unsignedBigInteger('category_id')->after('id');
            $table->string('type', 30)->after('category_id');
            $table->string('timeunit', 30)->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ms_service_level_agreements', function (Blueprint $table) {
            $table->dropColumn(['category_id', 'type', 'timeunit']);
        });
    }
};
