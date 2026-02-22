<?php

namespace App\Filament\Imports;

use App\Models\Competency;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class CompetencyImporter extends Importer
{
    protected static ?string $model = Competency::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Nombre de la competencia')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),

            ImportColumn::make('description')
                ->label('Descripción')
                ->rules(['nullable', 'string']),

            ImportColumn::make('duration_hours')
                ->label('Duración (horas)')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer', 'min:1']),

            // --- RELACIONES AUTOMÁTICAS ---
            ImportColumn::make('norm')
                ->relationship(resolveUsing: 'code') 
                ->label('Codigo de la norma laboral')
                ->rules(['nullable']),

            ImportColumn::make('competencyType')
                ->relationship(resolveUsing: 'name') 
                ->label('Tipo de competencia')
                ->rules(['nullable']),
        ];
    }

    public function resolveRecord(): ?Competency
    {
        return Competency::firstOrNew([
            'name' => $this->data['name'],
            'program_id' => $this->options['program_id'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Importación de competencias completada: ' . Number::format($import->successful_rows) . ' ' . str('fila')->plural($import->successful_rows) . ' procesadas.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('fila')->plural($failedRowsCount) . ' fallaron.';
        }

        return $body;
    }
}