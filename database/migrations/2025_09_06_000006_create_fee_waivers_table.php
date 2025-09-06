<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fee_waivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('fee_category_id')->constrained()->onDelete('cascade');
            $table->string('waiver_type'); // Scholarship, Economic hardship, Merit, etc.
            $table->decimal('waiver_percentage', 5, 2)->nullable();
            $table->decimal('waiver_amount', 10, 2)->nullable();
            $table->enum('waiver_method', ['percentage', 'fixed_amount'])->default('percentage');
            $table->date('effective_from');
            $table->date('effective_till')->nullable();
            $table->string('approved_by'); // Authority who approved
            $table->date('approval_date');
            $table->text('reason');
            $table->json('supporting_documents')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'is_active']);
            $table->index(['fee_category_id', 'waiver_type']);
            $table->index(['effective_from', 'effective_till']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('fee_waivers');
    }
};
