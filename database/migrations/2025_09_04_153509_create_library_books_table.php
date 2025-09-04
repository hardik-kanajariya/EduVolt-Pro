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
        Schema::create('library_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained('book_categories')->onDelete('cascade');
            $table->string('title');
            $table->string('author');
            $table->string('isbn', 20)->nullable();
            $table->string('publisher')->nullable();
            $table->year('publication_year')->nullable();
            $table->string('edition')->nullable();
            $table->text('description')->nullable();
            $table->string('language', 50)->default('English');
            $table->integer('pages')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('cover_image')->nullable();
            $table->string('barcode')->unique()->nullable();
            $table->integer('total_copies')->default(1);
            $table->integer('available_copies')->default(1);
            $table->integer('issued_copies')->default(0);
            $table->integer('reserved_copies')->default(0);
            $table->enum('condition', ['excellent', 'good', 'fair', 'poor'])->default('excellent');
            $table->string('location')->nullable(); // Shelf location
            $table->boolean('is_active')->default(true);
            $table->json('additional_info')->nullable(); // For extra fields
            $table->timestamps();
            $table->softDeletes();

            $table->index(['isbn']);
            $table->index(['school_id', 'category_id']);
            $table->index(['title', 'author']);
            $table->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('library_books');
    }
};
