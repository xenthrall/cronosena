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
        Schema::create('ficha_competency_executions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ficha_competency_id')
                ->constrained('ficha_competencies')
                ->restrictOnDelete();
                
            $table->foreignId('instructor_id')
                ->constrained('instructors')
                ->restrictOnDelete();

            // Optional: the training environment where this execution took place
            $table->foreignId('training_environment_id')
                ->nullable()
                ->constrained('training_environments')
                ->restrictOnDelete();

            $table->date('execution_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->integer('executed_hours')->default(0);

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ficha_competency_executions');
    }
};
