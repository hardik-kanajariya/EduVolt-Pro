<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fee_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('fee_installment_id')->constrained()->onDelete('cascade');
            $table->enum('reminder_type', ['email', 'sms', 'notification', 'letter']);
            $table->integer('reminder_number'); // 1st, 2nd, 3rd reminder
            $table->date('due_date');
            $table->date('sent_date');
            $table->enum('status', ['sent', 'delivered', 'failed', 'pending'])->default('pending');
            $table->text('message_content')->nullable();
            $table->string('recipient_contact')->nullable(); // Email or phone
            $table->json('delivery_details')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'due_date']);
            $table->index(['sent_date', 'status']);
            $table->index(['reminder_type', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('fee_reminders');
    }
};
