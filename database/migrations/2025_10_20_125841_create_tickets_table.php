<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id('ticket_id');
            $table->string('title');
            $table->text('description');
            $table->unsignedBigInteger('created_by'); // user who raised
            $table->unsignedBigInteger('assigned_to')->nullable(); // agent assigned
            $table->unsignedBigInteger('department_id')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('low');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed', 'reopened'])->default('open');
            $table->timestamps();
            $table->timestamp('closed_at')->nullable();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
