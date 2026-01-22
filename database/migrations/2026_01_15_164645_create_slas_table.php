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
    Schema::create('slas', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Contoh: "High", "Medium"
        $table->time('response_time');   // Format jam:menit (contoh: 00:15:00)
        $table->time('resolution_time'); // Format jam:menit (contoh: 02:00:00)
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slas');
    }
};
