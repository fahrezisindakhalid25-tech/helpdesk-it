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
        Schema::table('users', function (Blueprint $table) {
            $table->json('permissions')->nullable()->after('password');
        });

        // Migrate Data: Copy permissions from Role to User
        foreach (\App\Models\User::all() as $user) {
            if ($user->role && $user->role->permissions) {
                $user->permissions = $user->role->permissions;
                $user->save();
            } else {
                // If no role, default to empty or keep null
                $user->permissions = [];
                $user->save();
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        Schema::dropIfExists('roles');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a destructive migration, hard to fully reverse without data loss.
        // We will just restore the structure.
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->json('permissions')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
            $table->dropColumn('permissions');
        });
    }
};
