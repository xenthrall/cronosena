<?php

namespace App\Filament\Imports;

use App\Models\User;
use App\Models\Instructor;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InstructorImporter extends Importer
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            // Campos del User
            ImportColumn::make('name')->label('Nombre Completo')->requiredMapping()->rules(['required']),
            ImportColumn::make('email')->label('Correo electrónico')->requiredMapping()->rules(['required', 'email']),

            // Campos del Instructor (Filament los leerá del CSV, pero los ignorar en fillRecord)
            ImportColumn::make('document_number')->label('Número de documento')->requiredMapping(),
            ImportColumn::make('document_type')->label('Tipo de documento'),
            ImportColumn::make('first_name')->label('Nombres'),
            ImportColumn::make('last_name')->label('Apellidos'),
            ImportColumn::make('institutional_email')->label('Correo institucional'),
            ImportColumn::make('phone')->label('Teléfono'),
            ImportColumn::make('specialty')->label('Especialidad'),
        ];
    }

    public function resolveRecord(): User
    {
        return User::firstOrNew([
            'email' => $this->data['email']
        ]);
    }

    public function fillRecord(): void
    {
        // Solo llenamos los datos que PERTENECEN al usuario
        $this->record->name = $this->data['name'];
        $this->record->email = $this->data['email'];

        // Si es nuevo, configuramos password y estado
        if (! $this->record->exists) {
            $this->record->password = $this->data['document_number'];
            $this->record->is_active = true;
            $this->record->created_by = auth()->id();
        }
    }

    /**
     * Lógica del Instructor (se ejecuta después de guardar el User)
     */
    protected function afterSave(): void
    {
        Instructor::updateOrCreate(
            ['user_id' => $this->record->id],
            [
                'document_number'     => $this->data['document_number'],
                'document_type'       => $this->data['document_type'] ?? null,
                'first_name'          => $this->data['first_name'] ?? null,
                'last_name'           => $this->data['last_name'] ?? null,
                'institutional_email' => $this->data['institutional_email'] ?? null,
                'phone'               => $this->data['phone'] ?? null,
                'specialty'           => $this->data['specialty'] ?? null,
                'is_active'           => true,
            ]
        );
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Importación completada: ' . Number::format($import->successful_rows) . ' ' . str('fila')->plural($import->successful_rows) . ' procesadas.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('fila')->plural($failedRowsCount) . ' fallaron.';
        }

        return $body;
    }
}