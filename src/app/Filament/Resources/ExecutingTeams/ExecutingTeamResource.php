<?php

namespace App\Filament\Resources\ExecutingTeams;

use App\Filament\Resources\ExecutingTeams\Pages\ManageExecutingTeams;
use App\Models\ExecutingTeam;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class ExecutingTeamResource extends Resource
{
    protected static ?string $model = ExecutingTeam::class;

    protected static ?string $modelLabel = 'Equipo Ejecutor';

    protected static ?string $pluralModelLabel = 'Equipos Ejecutores';

    protected static ?string $navigationLabel = 'Equipo Ejecutor';

    protected static string|\UnitEnum|null $navigationGroup = 'instructores';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                TextInput::make('description')
                    ->label('Descripción'),
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
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->disabled(function (ExecutingTeam $record) {
                        return $record->instructors()->exists();
                    })
                    ->tooltip(function (ExecutingTeam $record) {
                        if ($record->instructors()->exists()) {
                            return 'No se puede eliminar porque tiene instructores asociados';
                        }
                        return null;
                    })
                    ->before(function (ExecutingTeam $record, DeleteAction $action) {
                        if ($record->instructors()->exists()) {
                            Notification::make()
                                ->title('No se puede eliminar el equipo ejecutor')
                                ->body('Este equipo ejecutor tiene instructores asociados. Por favor, reasigne a los instructores a otro equipo antes de eliminarlo.')
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
            'index' => ManageExecutingTeams::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->can('instructor.manageEquipoEjecutor');
    }
}
