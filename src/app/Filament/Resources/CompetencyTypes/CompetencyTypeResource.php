<?php

namespace App\Filament\Resources\CompetencyTypes;

use App\Filament\Resources\CompetencyTypes\Pages\ManageCompetencyTypes;
use App\Models\CompetencyType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class CompetencyTypeResource extends Resource
{
    protected static ?string $model = CompetencyType::class;

    protected static ?string $navigationLabel = 'Tipos de Competencias';

    protected static ?string $pluralModelLabel = 'Tipos de Competencias';

    protected static ?string $modelLabel = 'Tipo de Competencia';

    protected static string|\UnitEnum|null $navigationGroup = 'programas';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true) //  ocultar por defecto
                    ->tooltip(fn($record) => $record->description), //  muestra completo al pasar mouse

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
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->disabled(function (CompetencyType $record) {
                        return $record->competencies()->exists();
                    })
                    ->tooltip(function (CompetencyType $record) {
                        if ($record->competencies()->exists()) {
                            return 'No se puede eliminar porque está asociado a una o más competencias';
                        }
                        return null;
                    })
                    ->before(function (CompetencyType $record, DeleteAction $action) {
                        if ($record->competencies()->exists()) {
                            Notification::make()
                                ->title('No se puede eliminar el tipo de competencia')
                                ->body('Este tipo está siendo utilizado por una o más competencias. Debe reasignar esas competencias antes de eliminarlo.')
                                ->danger()
                                ->duration(8000)
                                ->send();

                            $action->cancel();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCompetencyTypes::route('/'),
        ];
    }
}
