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
        Schema::create('fichas', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique();

            // Key dates
            $table->date('start_date')->nullable();
            $table->date('lective_end_date')->nullable();
            $table->date('end_date')->nullable(); 

            // Relationships
            $table->foreignId('program_id')
                ->constrained('programs')
                ->restrictOnDelete();

            $table->foreignId('municipality_id')
                ->nullable()
                ->constrained('municipalities')
                ->restrictOnDelete();

            $table->foreignId('status_id')
                ->nullable()
                ->constrained('ficha_statuses')
                ->restrictOnDelete();

            $table->foreignId('shift_id')
                ->nullable()
                ->constrained('shifts')
                ->restrictOnDelete();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fichas');
    }
};
