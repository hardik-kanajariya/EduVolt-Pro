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
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('session_id')->nullable()->after('class_id')->constrained('attendance_sessions')->onDelete('cascade');
            $table->time('in_time')->nullable()->after('status');
            $table->time('out_time')->nullable()->after('in_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['session_id']);
            $table->dropColumn(['session_id', 'in_time', 'out_time']);
        });
    }
};
