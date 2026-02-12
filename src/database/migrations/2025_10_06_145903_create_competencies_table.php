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
        Schema::create('competencies', function (Blueprint $table) {
            $table->id();
            
            // Datos propios de la competencia del programa
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->unsignedInteger('duration_hours');

            $table->foreignId('program_id')
                ->constrained('programs')
                ->restrictOnDelete();

            $table->foreignId('norm_id')
                ->nullable()
                ->constrained('norms')
                ->restrictOnDelete();

            $table->foreignId('competency_type_id')
                ->nullable()
                ->constrained('competency_types')
                ->restrictOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competencies');
    }
};
