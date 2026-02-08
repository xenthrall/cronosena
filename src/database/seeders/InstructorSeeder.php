<?php

namespace Database\Seeders;

use App\Models\Instructor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class InstructorSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/instructors');
        $files = File::glob($path . '/*.json');

        $contadorInstructores = 0;

        foreach ($files as $file) {
            $jsonContent = File::get($file);
            $instructors = json_decode($jsonContent, true);

            if (!is_array($instructors)) {
                $this->command->warn("⚠️ Archivo inválido: {$file}");
                continue;
            }

            foreach ($instructors as $data) {
                // Validación mínima
                if (!isset($data['document_number'])) {
                    $this->command->warn("⚠️ Instructor sin document_number en {$file}");
                    continue;
                }

                $instructorData = [
                    'document_number'      => $data['document_number'],
                    'document_type'        => $data['document_type'] ?? 'CC',
                    'full_name'            => $data['full_name'] ?? null,
                    'name'                 => $data['name'] ?? null,
                    'last_name'            => $data['last_name'] ?? null,
                    'email'                => $data['email'] ?? null,
                    'institutional_email'  => $data['institutional_email'] ?? null,
                    'phone'                => $data['phone'] ?? null,
                    'executing_team_id'    => $data['executing_team_id'] ?? 2,

                    // Password por defecto: documento_2026cata
                    'password' => $data['document_number'] . '_2026cata',

                ];

                Instructor::updateOrCreate(
                    ['document_number' => $data['document_number']],
                    $instructorData
                );

                $contadorInstructores++;
            }
        }

        $this->command->info("✅ {$contadorInstructores} instructores creados correctamente.");
    }
}
