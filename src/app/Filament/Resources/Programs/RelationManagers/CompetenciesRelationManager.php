<?php

namespace App\Filament\Resources\Programs\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section as InfoSection;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Imports\CompetencyImporter;
use Filament\Actions\ImportAction;

use App\Models\Competency;
use Filament\Notifications\Notification;

class CompetenciesRelationManager extends RelationManager
{
    protected static string $relationship = 'competencies';

    protected static ?string $title = 'Competencias';

    protected static ?string $modelLabel = 'Competencia';


    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()->schema([
                    Select::make('norm_id')
                        ->label('Código Norma')
                        ->relationship('norm', 'code')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->code . ' - ' . $record->name)
                        ->searchable()
                        ->preload()
                        ->placeholder('Seleccione una norma...')
                        ->required()
                        ->columnSpanFull()
                        ->reactive()
                        ->afterStateUpdated(function (callable $set, $state) {
                            $norm = \App\Models\Norm::find($state);
                            if ($norm) {
                                $set('name', $norm->name);
                                $set('description', $norm->description);
                            }
                        }),

                    TextInput::make('name')
                        ->label('Nombre')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->placeholder('Ejemplo: Desarrollo de Aplicaciones Web'),

                ])->columns(3)->columnSpanFull(),
                Grid::make()->schema([
                    Select::make('competency_type_id')
                        ->label('Tipo de Competencia')
                        ->relationship('competencyType', 'name')
                        ->nullable()
                        ->preload(),

                    TextInput::make('duration_hours')
                        ->label('Duración (Horas)')
                        ->minValue(0)
                        ->numeric()
                        ->required(),
                ])->columns(3)->columnSpanFull(),

                Textarea::make('description')
                    ->label('Descripción')
                    ->rows(4)
                    ->placeholder('Descripción de la norma...')
                    ->columnSpanFull(),


            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                InfoSection::make('Detalles de la Competencia')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nombre')
                            ->columnSpanFull(),

                        TextEntry::make('norm.code')
                            ->label('Código Norma Laboral')
                            ->columnSpanFull(),

                        TextEntry::make('competencyType.name')
                            ->label('Tipo de Competencia'),

                        TextEntry::make('duration_hours')
                            ->label('Duración (Horas)')
                            ->numeric(),
                    ])
                    ->columns(2),

                InfoSection::make('Descripción')
                    ->schema([
                        TextEntry::make('description')
                            ->label('Descripción')
                            ->placeholder('Sin descripción')
                            ->wrap(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('norm.code')
                    ->label('Código Norma')
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->tooltip(fn($record) => $record->name)
                    ->limit(50),

                TextColumn::make('competencyType.name')
                    ->label('Tipo de Competencia')
                    ->badge(),

                TextColumn::make('duration_hours')
                    ->label('Duración (Horas)')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('version')
                    ->label('Versión')
                    ->alignCenter()
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->date('d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('competency_type_id')
                    ->label('Tipo de Competencia')
                    ->relationship('competencyType', 'name')
                    ->preload()
                    ->searchable(),
            ])
            ->headerActions([
                CreateAction::make(),
                ImportAction::make()
                    ->label('Importar competencias')
                    ->importer(CompetencyImporter::class)
                    ->options(function (RelationManager $livewire): array {
                        return [
                            // Capturamos el ID del programa actual
                            'program_id' => $livewire->getOwnerRecord()->id, 
                        ];
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->disabled(function (Competency $record) {
                        return $record->fichaCompetencies()->exists();
                    })
                    ->tooltip(fn (Competency $record) => $record->fichaCompetencies()->exists()
                        ? 'No se puede eliminar: Esta competencia ya está asignada a una o más Fichas.'
                        : null
                    )
                    ->before(function (Competency $record, DeleteAction $action) {
                        if ($record->fichaCompetencies()->exists()) {
                            Notification::make()
                                ->title('No se puede eliminar la competencia')
                                ->body('Esta competencia está siendo utilizada en Fichas activas. No se puede eliminar hasta que sea desvinculada de todas las fichas.')
                                ->danger()
                                ->duration(10000) 
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
}
