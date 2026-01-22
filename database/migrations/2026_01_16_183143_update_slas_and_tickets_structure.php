<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update Tabel Master SLA (Tambah Kolom Hari)
        Schema::table('slas', function (Blueprint $table) {
            if (!Schema::hasColumn('slas', 'response_days')) {
                $table->integer('response_days')->default(0)->after('name');
            }
            if (!Schema::hasColumn('slas', 'resolution_days')) {
                $table->integer('resolution_days')->default(0)->after('response_time');
            }
        });

        // 2. Update Tabel Tickets (Tambah Kolom SLA Resolusi Terpisah)
        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'resolution_sla_id')) {
                $table->foreignId('resolution_sla_id')
                      ->nullable()
                      ->after('sla_id')
                      ->constrained('slas')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('slas', function (Blueprint $table) {
            $table->dropColumn(['response_days', 'resolution_days']);
        });
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['resolution_sla_id']);
            $table->dropColumn('resolution_sla_id');
        });
    }
};