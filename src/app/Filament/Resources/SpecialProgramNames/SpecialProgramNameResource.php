<?php

namespace App\Filament\Resources\SpecialProgramNames;

use App\Filament\Resources\SpecialProgramNames\Pages\ManageSpecialProgramNames;
use App\Models\SpecialProgramName;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class SpecialProgramNameResource extends Resource
{
    protected static ?string $model = SpecialProgramName::class;

    protected static ?string $navigationLabel = 'Programas Especiales';

    protected static ?string $pluralModelLabel = 'Programas Especiales';

    protected static ?string $modelLabel = 'Programa Especial';

    protected static string|\UnitEnum|null $navigationGroup = 'programas';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre del Programa Especial')
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre del Programa Especial')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->label('Actualización')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->disabled(function (SpecialProgramName $record) {
                        return $record->programs()->exists();
                    })
                    ->tooltip(function (SpecialProgramName $record) {
                        if ($record->programs()->exists()) {
                            return 'No se puede eliminar porque está asociado a uno o más programas';
                        }
                        return null;
                    })
                    ->before(function (SpecialProgramName $record, DeleteAction $action) {
                        if ($record->programs()->exists()) {
                            Notification::make()
                                ->title('No se puede eliminar')
                                ->body('Este nombre de programa especial está siendo utilizado por uno o más programas. Por favor, reasigne esos programas antes de eliminarlo.')
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
            'index' => ManageSpecialProgramNames::route('/'),
        ];
    }
}
