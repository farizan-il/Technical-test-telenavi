<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // NOT NULL by default
            $table->string('assignee')->nullable();
            $table->date('due_date');
            $table->unsignedInteger('time_tracked')->default(0);
            $table->enum('status', ['pending', 'open', 'in_progress', 'completed'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high']);
            $table->timestamps(); // creates created_at and updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};