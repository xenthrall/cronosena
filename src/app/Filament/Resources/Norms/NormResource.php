<?php

namespace App\Filament\Resources\Norms;

use App\Filament\Resources\Norms\Pages\ManageNorms;
use App\Models\Norm;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class NormResource extends Resource
{
    protected static ?string $model = Norm::class;

    protected static ?string $navigationLabel = 'Normas Laborales';
    protected static ?string $pluralModelLabel = 'Normas Laborales';
    protected static ?string $modelLabel = 'Norma Laboral';
    protected static string|\UnitEnum|null $navigationGroup = 'programas';
    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Código Norma Laboral')
                    ->unique()
                    ->required(),
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                Textarea::make('description')
                    ->label('Descripción')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('code')
                    ->label('Código Norma Laboral')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->limit(50)
                    ->tooltip(fn($record) => $record->name)
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(80)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip(fn($record) => $record->description),
                TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Fecha de Actualización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->disabled(function (Norm $record) {
                        // Deshabilitar si tiene competencias o instructores asociados
                        return $record->competencies()->exists() || $record->instructors()->exists();
                    })
                    ->tooltip(function (Norm $record) {
                        if ($record->competencies()->exists() || $record->instructors()->exists()) {
                            return 'No se puede eliminar porque está asociada a competencias o instructores';
                        }
                        return null;
                    })
                    ->before(function (Norm $record, DeleteAction $action) {
                        // Verificación de seguridad antes de ejecutar
                        if ($record->competencies()->exists() || $record->instructors()->exists()) {

                            // Determinamos el mensaje exacto para ser más claros con el usuario
                            $motivo = [];
                            if ($record->competencies()->exists()) $motivo[] = 'competencias';
                            if ($record->instructors()->exists()) $motivo[] = 'instructores';
                            $motivoTexto = implode(' e ', $motivo);

                            Notification::make()
                                ->title('No se puede eliminar la norma')
                                ->body("Esta norma está siendo utilizada por $motivoTexto. Elimine las asociaciones primero.")
                                ->danger()
                                ->duration(8000)
                                ->send();

                            $action->cancel();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageNorms::route('/'),
        ];
    }
}
