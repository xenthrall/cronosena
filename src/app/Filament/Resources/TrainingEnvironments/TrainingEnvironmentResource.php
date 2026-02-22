<?php

namespace App\Filament\Resources\TrainingEnvironments;

use App\Filament\Resources\TrainingEnvironments\Pages\ManageTrainingEnvironments;
use App\Models\TrainingEnvironment;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class TrainingEnvironmentResource extends Resource
{
    protected static ?string $model = TrainingEnvironment::class;

    protected static ?string $navigationLabel = 'Ambientes';
    protected static ?string $pluralModelLabel = 'Ambientes de formación';
    protected static ?string $modelLabel = 'Ambiente de formación';
    protected static string|\UnitEnum|null $navigationGroup = 'fichas';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Código')
                    ->placeholder('ejem: LAB-206')
                    ->required(),
                TextInput::make('name')
                    ->placeholder('ejem: LABORATORIO-206')
                    ->label('Nombre'),
                TextInput::make('capacity')
                    ->label('Capacidad')
                    ->nullable()
                    ->placeholder('Número máximo de aprendices')
                    ->integer(),
                Select::make('location_id')
                    ->label('Ubicación')
                    ->required()
                    ->preload()
                    ->searchable()
                    ->relationship('location', 'name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('code')
                    ->label('Código')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('capacity')
                    ->label('Capacidad')
                    ->sortable(),
                TextColumn::make('location.name')
                    ->label('Ubicación')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Fecha de creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Fecha de actualización')
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
                    ->disabled(function (TrainingEnvironment $record) {
                        return $record->competencyExecutions()->exists();
                    })
                    ->tooltip(function (TrainingEnvironment $record) {
                        if ($record->competencyExecutions()->exists()) {
                            return 'No se puede eliminar porque tiene ejecuciones programadas asociadas';
                        }
                        return null;
                    })
                    ->before(function (TrainingEnvironment $record, DeleteAction $action) {
                        if ($record->competencyExecutions()->exists()) {
                            Notification::make()
                                ->title('No se puede eliminar el ambiente')
                                ->body('Este ambiente de formación tiene ejecuciones o programaciones asociadas. Por favor, libere el ambiente de esas ejecuciones antes de eliminarlo.')
                                ->danger()
                                ->duration(8000)
                                ->send();

                            $action->cancel();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTrainingEnvironments::route('/'),
        ];
    }
}
