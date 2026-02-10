<?php

namespace App\Filament\Resources\Competencies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;

class CompetencyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select ::make("program_id")
                    ->label("Programa")
                    ->relationship("program", "name")
                    ->required()
                    ->searchable()
                    ->preload(),
                    
                Select::make('competency_type_id')
                    ->label('Tipo de Competencia')
                    ->relationship('competencyType', 'name')
                    ->nullable()
                    ->searchable()
                    ->preload(),

                Select::make('norm_id')
                    ->label('Código Norma Laboral')
                    ->relationship('norm', 'code')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                TextInput::make('duration_hours')
                    ->label('Duración (Horas)')
                    ->integer()
                    ->required(),

                Textarea::make('description')
                    ->label('Descripción')
                    ->rows(4)
                    ->columnSpanFull(),

                
            ]);
    }
}
