<?php

namespace App\Filament\Resources\Programs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class ProgramForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('program_code')
                    ->label('Código del Programa')
                    ->required()
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn(Unique $rule, callable $get) => $rule->where('version', $get('version'))
                    )
                    ->validationMessages([
                        'unique' => 'Este Código de Programa ya se encuentra registrado con la versión actual.',
                    ]),

                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),

                TextInput::make('total_duration_hours')
                    ->label('Duración (Horas)')
                    ->required()
                    ->numeric(),

                Select::make('training_level_id')
                    ->label('Nivel de Formación')
                    ->relationship('trainingLevel', 'name')
                    ->nullable(),

                Select::make('special_program_name_id')
                    ->label('Nombre del Programa Especial')
                    ->relationship('specialProgramName', 'name')
                    ->nullable()
                    ->searchable()
                    ->preload(),

                TextInput::make('version')
                    ->label('Versión')
                    ->required()
                    ->maxLength(20)
                    ->default('1')
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn(Unique $rule, callable $get) => $rule->where('program_code', $get('program_code'))
                    )
                    ->validationMessages([
                        'unique' => 'Esta versión ya existe para el Código de Programa ingresado. Intenta con una nueva versión.',
                    ]),
            ]);
    }
}
