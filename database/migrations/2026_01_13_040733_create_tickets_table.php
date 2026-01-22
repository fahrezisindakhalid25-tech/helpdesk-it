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
    Schema::create('tickets', function (Blueprint $table) {
        $table->id();
        
        // --- SEKARANG KITA PAKAI "LOKASI" (BIAR SAMA DENGAN PDF) ---
        $table->string('no_tiket')->unique(); 
        $table->string('lokasi');             // GANTI DARI 'bagian' JADI 'lokasi'
        $table->string('nama_lengkap');       
        $table->string('topik_bantuan');      
        $table->text('deskripsi_umum_masalah'); 
        $table->string('status')->default('Open'); 

        $table->string('email')->nullable();    
        $table->string('no_hp')->nullable();    
        $table->longText('penjelasan_lengkap')->nullable(); 
        
        $table->timestamps(); 
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
