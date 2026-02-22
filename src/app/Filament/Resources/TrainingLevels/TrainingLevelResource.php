<?php

namespace App\Filament\Resources\TrainingLevels;

use App\Filament\Resources\TrainingLevels\Pages\ManageTrainingLevels;
use App\Models\TrainingLevel;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class TrainingLevelResource extends Resource
{
    protected static ?string $model = TrainingLevel::class;

    protected static ?string $navigationLabel = 'Niveles de Formación';

    protected static ?string $pluralModelLabel = 'Niveles de Formación';

    protected static ?string $modelLabel = 'Nivel de Formación';

    protected static string|\UnitEnum|null $navigationGroup = 'programas';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->unique()
                    ->maxLength(50)
                    ->required(),
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
                TextColumn::make('created_at')
                    ->label('Creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Actualización')
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
                    ->disabled(function (TrainingLevel $record) {
                        return $record->programs()->exists();
                    })
                    ->tooltip(function (TrainingLevel $record) {
                        if ($record->programs()->exists()) {
                            return 'No se puede eliminar porque está asociado a uno o más programas';
                        }
                        return null;
                    })
                    ->before(function (TrainingLevel $record, DeleteAction $action) {
                        if ($record->programs()->exists()) {
                            Notification::make()
                                ->title('No se puede eliminar el nivel de formación')
                                ->body('Este nivel de formación está siendo utilizado por uno o más programas. Por favor, reasigne esos programas antes de eliminarlo.')
                                ->danger()
                                ->duration(8000)
                                ->send();

                            $action->cancel();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([

                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTrainingLevels::route('/'),
        ];
    }
}
