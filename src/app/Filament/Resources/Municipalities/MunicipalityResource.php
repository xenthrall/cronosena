<?php

namespace App\Filament\Resources\Municipalities;

use App\Filament\Resources\Municipalities\Pages\ManageMunicipalities;
use App\Models\Municipality;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class MunicipalityResource extends Resource
{
    protected static ?string $model = Municipality::class;

    protected static ?string $modelLabel = 'Municipio';
    
    protected static ?string $pluralModelLabel = 'Municipios';

    protected static ?string $navigationLabel = 'Municipios';

    protected static string|\UnitEnum|null $navigationGroup = 'fichas';

    protected static ?int $navigationSort = 3;
    
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
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
                    ->disabled(function (Municipality $record) {
                        return $record->locations()->exists() || $record->fichas()->exists();
                    })
                    ->tooltip(function (Municipality $record) {
                        if ($record->locations()->exists() || $record->fichas()->exists()) {
                            return 'No se puede eliminar porque está asociado a sedes o fichas';
                        }
                        return null;
                    })
                    ->before(function (Municipality $record, DeleteAction $action) {
                        if ($record->locations()->exists() || $record->fichas()->exists()) {
                            
                            // Determinamos exactamente dónde se está usando para el mensaje
                            $motivos = [];
                            if ($record->locations()->exists()) $motivos[] = 'sedes';
                            if ($record->fichas()->exists()) $motivos[] = 'fichas';
                            $motivoTexto = implode(' y ', $motivos);

                            Notification::make()
                                ->title('No se puede eliminar el municipio')
                                ->body("Este municipio está siendo utilizado por $motivoTexto. Por favor, reasigne estos registros antes de eliminarlo.")
                                ->danger()
                                ->duration(8000)
                                ->send();

                            $action->cancel();
                        }
                    }),
            ])
            ->toolbarActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageMunicipalities::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->can('ficha.municipalities');
    }
}
