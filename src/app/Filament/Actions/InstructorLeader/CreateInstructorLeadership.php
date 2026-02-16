<?php

namespace App\Filament\Actions\InstructorLeader;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Alignment;
use App\Models\Instructor;
use App\Models\FichaInstructorLeadership;

class CreateInstructorLeadership extends Action
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'createInstructorLeadership')
            ->label('Asignar Instructor Líder')
            ->icon('heroicon-o-plus-circle')
            ->color('success')
            ->modalHeading('Asignar Instructor Líder')
            ->modalIcon('heroicon-o-user-plus')
            ->modalSubmitActionLabel('Crear asignación')
            ->schema([
                Select::make('instructor_id')
                    ->label('Instructor')
                    ->options(Instructor::all()->pluck('full_label', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),

                DatePicker::make('start_date')
                    ->label('Fecha de inicio')
                    ->required()
                    ->default(now()),
            ])
            ->modalAlignment(Alignment::Center)
            ->action(function (array $data, $record) {

                FichaInstructorLeadership::create([
                    'ficha_id'      => $record->id, // El record es una Ficha
                    'instructor_id' => $data['instructor_id'],
                    'start_date'    => $data['start_date'],
                    'end_date'      => null, // No se define al crear
                ]);
            })
            ->successNotificationTitle('Instructor líder asignado con éxito.');
    }
}
