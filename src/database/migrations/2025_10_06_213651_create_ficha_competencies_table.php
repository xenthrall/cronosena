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
        Schema::create('ficha_competencies', function (Blueprint $table) {
            $table->id();

             // Relationships
            $table->foreignId('ficha_id')
                ->constrained('fichas')
                ->onDelete('cascade');

            $table->foreignId('competency_id')
                ->constrained('competencies')
                ->restrictOnDelete();

            // Custom fields
            $table->integer('order')->default(1);
            $table->integer('total_hours_competency')->default(0);
            $table->integer('executed_hours')->default(0);

            $table->string('status')->default('pendiente'); // pendiente, en_progreso, completado

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ficha_competencies');
    }
};
