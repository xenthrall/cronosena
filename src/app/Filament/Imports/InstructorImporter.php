<?php

namespace App\Filament\Imports;

use App\Models\Instructor;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class InstructorImporter extends Importer
{
    protected static ?string $model = Instructor::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('document_number')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('document_type'),
            ImportColumn::make('full_name'),
            ImportColumn::make('name'),
            ImportColumn::make('last_name'),
            ImportColumn::make('email'),
            ImportColumn::make('institutional_email'),
            ImportColumn::make('phone'),
            ImportColumn::make('specialty'),
        ];
    }

    public function resolveRecord(): Instructor
    {
        return Instructor::firstOrNew([
            'document_number' => $this->data['document_number'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your instructor import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
