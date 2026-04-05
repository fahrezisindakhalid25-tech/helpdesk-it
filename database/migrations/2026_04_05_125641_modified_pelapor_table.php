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
        Schema::rename('master_lapors', 'ms_pelapor');

        Schema::table('ms_pelapor', function (Blueprint $table) {
            $table->renameColumn('nik', 'NIK');
        });

        Schema::table('ms_pelapor', function (Blueprint $table) {
            $table->string('NIK', 16)->change();
            $table->string('nama', 80)->change();
            $table->string('email', 80)->change();
            $table->string('no_hp', 14)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('ms_pelapor', 'master_lapors');
        Schema::table('master_lapors', function (Blueprint $table) {
            $table->renameColumn('NIK', 'nik')->change();
        });

        Schema::table('master_lapors', function (Blueprint $table) {
            $table->string('nik')->change();
            $table->string('nama')->change();
            $table->string('email')->change();
            $table->string('no_hp')->change();
        });
    }
};
