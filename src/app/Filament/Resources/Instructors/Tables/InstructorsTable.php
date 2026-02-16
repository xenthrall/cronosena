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

use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;


class InstructorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('user.photo_url')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('full_name')
                    ->label('Instructor')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('document_number')
                    ->label('Documento')
                    ->searchable()
                    ->formatStateUsing(fn($state, $record) => "{$record->document_type} {$record->document_number}"),

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

                TextColumn::make('user.email')
                    ->label('Correo')
                    ->searchable()
                    ->default('Sin usuario de ingreso')
                    ->color(fn($record) => $record->user_id ? 'success' : 'danger')
                    ->tooltip(
                        fn($record) =>
                        $record->user_id
                            ? $record->user->email
                            : 'Este instructor no tiene usuario para ingresar al sistema'
                    )
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
                TernaryFilter::make('has_user')
                    ->label('Acceso al sistema')
                    ->placeholder('Todos los instructores')
                    ->trueLabel('Con usuario asignado')
                    ->falseLabel('Sin usuario asignado')
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('user_id'),
                        false: fn(Builder $query) => $query->whereNull('user_id'),
                    ),

                TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->placeholder('Todos')
                    ->trueLabel('Activos')
                    ->falseLabel('Inactivos')
                    ->boolean(), 
            ])

            ->recordActions([
                EditAction::make()
                    ->visible(fn() => Auth::user()?->can('instructor.edit')),
            ])
            ->toolbarActions([
                ImportAction::make()
                    ->label('Importar instructores')
                    ->importer(InstructorImporter::class),
                BulkActionGroup::make([]),
            ]);
    }
}
