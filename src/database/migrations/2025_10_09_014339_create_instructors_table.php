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
        Schema::create('instructors', function (Blueprint $table) {
            $table->id();

            // Relación 1:1 con users
            $table->foreignId('user_id')
                ->nullable()
                ->unique()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('document_number')->unique();
            $table->string('document_type')->nullable();

            $table->string('first_name');
            $table->string('last_name');

            // Contacto institucional
            $table->string('institutional_email')->nullable()->unique();
            $table->string('phone')->nullable();

            $table->foreignId('executing_team_id')
                ->nullable()
                ->constrained('executing_teams')
                ->nullOnDelete();

            $table->string('specialty')->nullable();

            // Estado académico
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructors');
    }
};
