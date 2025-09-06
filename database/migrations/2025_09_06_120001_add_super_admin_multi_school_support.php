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
        // Add super admin fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('last_panel_accessed', 50)->nullable()->after('is_super_admin');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_super_admin', 'last_panel_accessed']);
        });
    }
};
