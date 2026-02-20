<?php

namespace App\Filament\Resources\Shifts;

use App\Filament\Resources\Shifts\Pages\ManageShifts;
use App\Models\Shift;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\CheckboxList;
use Filament\Tables\Columns\ColorColumn;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class ShiftResource extends Resource
{
    protected static ?string $model = Shift::class;

    protected static ?string $navigationLabel = 'Jornadas';

    protected static ?string $pluralModelLabel = 'Jornadas';

    protected static ?string $modelLabel = 'Jornada';

    protected static string|\UnitEnum|null $navigationGroup = 'fichas';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre de la jornada')
                    ->required()
                    ->maxLength(100)
                    ->columnSpanFull(), // Ocupa todo el ancho

                Textarea::make('description')
                    ->label('Descripción')
                    ->maxLength(255)
                    ->columnSpanFull(),

                // --- HORARIO SIMPLE (Se muestra si 'es_mixta' es falso) ---
                TimePicker::make('start_time')
                    ->label('Hora de inicio')
                    ->seconds(false)
                    ->displayFormat('h:i A')
                    ->native(false)
                    ->visible(fn($get) => !$get('is_mixed')),

                TimePicker::make('end_time')
                    ->label('Hora de fin')
                    ->seconds(false)
                    ->displayFormat('h:i A')
                    ->native(false)
                    ->visible(fn($get) => !$get('is_mixed')),

                // --- HORARIO MIXTO (Se muestra si 'es_mixta' es verdadero) ---
                Repeater::make('segments')
                    ->label('Segmentos horarios')
                    ->columns(2)
                    ->schema([
                        TimePicker::make('inicio')
                            ->label('Inicio del segmento')
                            ->seconds(false)
                            ->displayFormat('h:i A')
                            ->native(false)
                            ->required(),
                        TimePicker::make('fin')
                            ->label('Fin del segmento')
                            ->seconds(false)
                            ->displayFormat('h:i A')
                            ->native(false)
                            ->required(),
                    ])
                    ->visible(fn($get) => $get('is_mixed'))
                    ->columnSpanFull(),

                CheckboxList::make('valid_days')
                    ->label('Días hábiles / válidos')
                    ->options([
                        'Lunes' => 'Lunes',
                        'Martes' => 'Martes',
                        'Miércoles' => 'Miércoles',
                        'Jueves' => 'Jueves',
                        'Viernes' => 'Viernes',
                        'Sábado' => 'Sábado',
                        'Domingo' => 'Domingo',
                    ])
                    ->columns(4)
                    ->required()
                    ->columnSpanFull(),
                 ColorPicker::make('color')
                    ->label('Color')
                    ->default('#4096b8ff')
                    ->required(),

                Toggle::make('is_mixed')
                    ->label('¿Es una jornada mixta (dividida)?')
                    ->live() // CLAVE: Dispara la actualización para mostrar/ocultar campos.
                    ->default(false),

                Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                ColorColumn::make('color')
                    ->label('Color'),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('start_time')
                    ->label('Hora Inicio')
                    ->time('h:i A')
                    ->placeholder('N/A (Mixta)')
                    ->sortable(),
                TextColumn::make('end_time')
                    ->label('Hora Fin')
                    ->time('h:i A')
                    ->placeholder('N/A (Mixta)')
                    ->sortable(),
                TextColumn::make('description')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('valid_days')
                    ->label('Días Válidos')
                    ->formatStateUsing(fn($state) => is_array($state) ? implode(', ', $state) : $state)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap(),
                IconColumn::make('is_mixed')
                    ->label('Mixta')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->disabled(function (Shift $record) {
                        return $record->fichas()->exists();
                    })
                    ->tooltip(function (Shift $record) {
                        if ($record->fichas()->exists()) {
                            return 'No se puede eliminar porque está asociada a una o más fichas';
                        }
                        return null;
                    })
                    ->before(function (Shift $record, DeleteAction $action) {
                        if ($record->fichas()->exists()) {
                            Notification::make()
                                ->title('No se puede eliminar la jornada')
                                ->body('Esta jornada está siendo utilizada por una o más fichas. Por favor, asigne otra jornada a esas fichas antes de eliminarla.')
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
            'index' => ManageShifts::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->can('ficha.shifts');
    }
}
