<?php

namespace App\Filament\Resources\Locations;

use App\Filament\Resources\Locations\Pages\ManageLocations;
use App\Models\Location;
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

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static ?string $modelLabel = 'Ubicación';
    
    protected static ?string $pluralModelLabel = 'Ubicaciones';

    protected static ?string $navigationLabel = 'Ubicaciones';

    protected static string|\UnitEnum|null $navigationGroup = 'fichas';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->placeholder('Nombre de la ubicación')
                    ->required(),
                TextInput::make('address')
                    ->placeholder('ejemplo: Calle 123 #45-67')
                    ->label('Dirección'),
                TextInput::make('description')
                    ->label('Descripción'),
                Select::make('municipality_id')
                    ->label('Municipio')
                    ->required()
                    ->preload()
                    ->searchable()
                    ->relationship('municipality', 'name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->limit(50)
                    ->tooltip(fn ($record): ?string => $record->name)
                    ->searchable(),
                TextColumn::make('address')
                    ->label('Dirección')
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50)
                    ->tooltip(fn ($record): ?string => $record ->description)
                    ->searchable(),
                TextColumn::make('municipality.name')
                    ->label('Municipio'),
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
                    ->disabled(function (Location $record) {
                        return $record->trainingEnvironments()->exists();
                    })
                    ->tooltip(function (Location $record) {
                        if ($record->trainingEnvironments()->exists()) {
                            return 'No se puede eliminar porque tiene ambientes de formación asociados';
                        }
                        return null;
                    })
                    ->before(function (Location $record, DeleteAction $action) {
                        if ($record->trainingEnvironments()->exists()) {
                            Notification::make()
                                ->title('No se puede eliminar la sede')
                                ->body('Esta sede tiene ambientes de formación asociados. Por favor, reasigne o elimine estos ambientes antes de borrar la sede.')
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
            'index' => ManageLocations::route('/'),
        ];
    }
}
