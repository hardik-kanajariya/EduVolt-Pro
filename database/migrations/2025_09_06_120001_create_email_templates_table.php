<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('subject');
            $table->longText('content');
            $table->json('variables')->nullable(); // Available template variables
            $table->enum('type', ['system', 'custom', 'bulk'])->default('custom');
            $table->enum('category', [
                'academic',
                'attendance',
                'fees',
                'events',
                'examinations',
                'library',
                'announcements',
                'emergency'
            ])->default('announcements');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->integer('usage_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['type', 'category']);
            $table->index(['is_active']);
            $table->index(['slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
