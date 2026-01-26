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
        $table->string('name'); 
        $table->string('number')->nullable();
        
        $table->string('response_days')->nullable();
        $table->time('response_time')->nullable(); 
        $table->integer('resolution_days')->default(0);

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
