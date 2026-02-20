<?php

namespace App\Filament\Imports;

use App\Models\Program;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class ProgramImporter extends Importer
{
    protected static ?string $model = Program::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('program_code')
                ->label('Código del programa')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),

            ImportColumn::make('version')
                ->label('Versión')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),

            ImportColumn::make('name')
                ->label('Nombre del programa')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),

            ImportColumn::make('total_duration_hours')
                ->label('Duración total (horas)')
                ->requiredMapping()
                ->numeric()
                ->rules(['nullable', 'integer', 'min:1']),

            // --- BÚSQUEDA EN TABLAS RELACIONADAS ---
            // Filament buscará el texto del CSV en la columna 'name' de la tabla 'training_levels'
            ImportColumn::make('trainingLevel')
                ->relationship(resolveUsing: 'name') 
                ->label('Nivel de formación')
                ->rules(['nullable']),

            // Filament buscará el texto del CSV en la columna 'name' de la tabla 'special_program_names'
            ImportColumn::make('specialProgramName')
                ->relationship(resolveUsing: 'name') 
                ->label('Programa especial')
                ->rules(['nullable']),
        ];
    }

    public function resolveRecord(): ?Program
    {
        return Program::firstOrNew([
            'program_code' => $this->data['program_code'],
            'version' => $this->data['version'] ?? '1',
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Importación de programas completada: ' . Number::format($import->successful_rows) . ' ' . str('fila')->plural($import->successful_rows) . ' procesadas.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('fila')->plural($failedRowsCount) . ' fallaron.';
        }

        return $body;
    }
}