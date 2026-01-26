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
        $table->uuid('uuid')->nullable()->unique()->index(); // Tambahan UUID
        
        $table->string('no_tiket')->unique()->index(); 
        $table->string('lokasi');
        $table->string('nama_lengkap');       
        $table->string('topik_bantuan');      
        
        // Relasi SLA
        // Relasi SLA
        $table->foreignId('sla_id')->nullable()->constrained('slas')->nullOnDelete();
        $table->timestamp('sla_due_at')->nullable();
        
        $table->foreignId('resolution_sla_id')->nullable()->constrained('slas')->nullOnDelete();
        $table->timestamp('resolution_due_at')->nullable();
        
        $table->text('deskripsi_umum_masalah'); 
        $table->string('status')->default('Open'); 

        $table->string('email')->nullable();    
        $table->string('no_hp')->nullable();    
        $table->longText('penjelasan_lengkap')->nullable(); 
        $table->json('gambar')->nullable(); // Langsung JSON
        
        // Tracking Waktu
        $table->timestamp('replied_at')->nullable();
        $table->timestamp('solved_at')->nullable();
        $table->timestamp('closed_at')->nullable();
        $table->timestamp('reopened_at')->nullable();
        $table->timestamp('last_reply_at')->nullable();

        $table->timestamps();
        $table->index('created_at'); // Index untuk sorting
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
