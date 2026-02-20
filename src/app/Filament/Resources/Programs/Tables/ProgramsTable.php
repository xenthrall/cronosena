<?php

namespace App\Filament\Resources\Programs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use App\Filament\Imports\ProgramImporter;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class ProgramsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('program_code')
                     ->label('Codigo')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->name)
                    ->searchable(),
                TextColumn::make('version')
                    ->label('Versión')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total_duration_hours')
                    ->label('Duración (Horas)')
                    ->numeric(),
                TextColumn::make('trainingLevel.name')
                    ->label('Nivel de Formación')
                    ->default('Sin asignar')
                    ->badge()
                    ->colors([
                        'gray' => fn($state): bool => $state === 'Sin asignar',
                        //'gray' => fn($state): bool => in_array($state, ['tecnico', 'Tecnólogo', 'Profesional']),
                    ])
                    ->icons([
                        'heroicon-m-x-circle' => fn($state): bool => $state === 'Sin asignar',
                    ]),
                TextColumn::make('specialProgramName.name')
                    ->label('Nombre Programa Especial')
                    ->default('Sin asignar')
                    ->badge()
                    ->colors([
                        'gray'    => fn($state): bool => $state === 'Sin asignar', // si no tiene tipo asignado
                    ])
                    ->icons([
                        'heroicon-m-x-circle' => fn($state): bool => $state === 'Sin asignar',
                    ]),
                    
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //Filtras por nivel de formación
                SelectFilter::make('training_level_id')
                     ->label('Nivel de Formación')
                    ->relationship('trainingLevel', 'name', hasEmptyOption: true)
                    ->emptyRelationshipOptionLabel('Sin asignar'),
                //Filtras por nombre de programa especial
                SelectFilter::make('special_program_name_id')
                    ->label('Nombre Programa Especial')
                    ->relationship('specialProgramName', 'name', hasEmptyOption: true)
                    ->emptyRelationshipOptionLabel('Sin asignar'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                ImportAction::make()
                    ->label('Importar programas')
                    ->importer(ProgramImporter::class),
                BulkActionGroup::make([
                    //DeleteBulkAction::make(),
                ]),
            ]);
    }
}
