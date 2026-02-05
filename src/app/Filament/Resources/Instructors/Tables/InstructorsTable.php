<?php

namespace App\Filament\Resources\Instructors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Facades\Auth;

use App\Filament\Imports\InstructorImporter;
use Filament\Actions\ImportAction;


class InstructorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo_url')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->toggleable(false),
                TextColumn::make('full_name')
                    ->label('Instructor')
                    ->searchable() // busca en esta columna
                    ->wrap(),
                TextColumn::make('document_number')
                    ->label('Documento')
                    ->searchable()
                    ->formatStateUsing(fn ($state, $record) => "{$record->document_type} {$record->document_number}"),

                TextColumn::make('executingTeam.name')
                    ->label('Equipo ejecutor')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('specialty')
                    ->label('Especialidad')
                    ->searchable()
                    ->limit(25)
                    //->wrap()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip(fn($record) => $record->specialty),

                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('institutional_email')
                    ->label('Correo institucional')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn() => Auth::user()?->can('instructor.edit')),
            ])
            ->toolbarActions([
                ImportAction::make()
                    ->label('Importar instructores')
                    ->importer(InstructorImporter::class),
                BulkActionGroup::make([

                ]),
            ]);
    }
}
