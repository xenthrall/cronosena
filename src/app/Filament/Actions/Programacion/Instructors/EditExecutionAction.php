<?php

namespace App\Filament\Actions\Programacion\Instructors;

use Filament\Actions\Action;
use Filament\Support\Enums\Alignment;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\{
    DatePicker,
    TextInput,
    Select
};

use Illuminate\Validation\ValidationException;
use Filament\Notifications\Notification;

use App\Models\Ficha;
use App\Models\FichaCompetency;
use App\Models\FichaCompetencyExecution;

use App\Traits\Executions\PreventsDateOverlap;
use App\Traits\Executions\CalculatesExecutionHours;

class EditExecutionAction extends Action
{
    use PreventsDateOverlap;
    use CalculatesExecutionHours;

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->name('editExecution')
            ->modal()
            ->label('Editar ejecución')
            ->icon('heroicon-o-pencil')
            ->record(function (array $arguments) {
                if (! isset($arguments['execution_id'])) {
                    return null;
                }

                return FichaCompetencyExecution::find($arguments['execution_id']);
            })

            ->color('primary')
            ->modalHeading('Editar ejecución')
            ->modalSubmitActionLabel('Guardar cambios')
            ->modalAlignment(Alignment::Center)

            ->schema([
                Grid::make(1)->schema([
                    TextInput::make('ficha_id')
                        ->label('Ficha')
                        ->disabled(),

                    TextInput::make('ficha_competency_id')
                        ->label('Competencia')
                        ->disabled(),
                ]),

                Grid::make(2)->schema([
                    DatePicker::make('execution_date')
                        ->label('Fecha inicio')
                        ->required()
                        ->disabled(),

                    DatePicker::make('completion_date')
                        ->label('Fecha fin')
                        ->required()
                        ->disabled(),
                ]),

                TextInput::make('executed_hours')
                    ->label('Horas ejecutadas')
                    ->integer()
                    ->disabled()
                    ->minValue(1)
                    ->maxValue(fn($record) => $record->fichaCompetency->remaining_hours + $record->executed_hours)
                    ->placeholder(fn($record) => "Máximo: " . ($record->fichaCompetency->remaining_hours + $record->executed_hours) . " horas")
                    ->default(fn($record) => $record->executed_hours)
                    ->required(),

                /** Ambiente */
                Grid::make(2)->schema([
                    Select::make('location_id')
                        ->label('Sede')
                        ->searchable()
                        ->reactive()
                        ->options(fn() => self::obtenerUbicaciones()),

                    Select::make('training_environment_id')
                        ->label('Ambiente')
                        ->searchable()
                        ->options(
                            fn(callable $get) =>
                            self::findAvailableTrainingEnvironments(
                                $get('execution_date'),
                                $get('completion_date'),
                                $get('location_id')
                            )
                        ),
                ]),

            ])


            ->fillForm(function ($record): array {
                if (! $record instanceof FichaCompetencyExecution) {
                    return [];
                }

                return [
                    'ficha_id' => $record->fichaCompetency?->ficha->code . ' - ' . $record->fichaCompetency?->ficha->program->name,
                    'ficha_competency_id' => $record->fichaCompetency?->competency->name ?? '',
                    'execution_date' => $record->execution_date,
                    'completion_date' => $record->completion_date,
                    'executed_hours' => $record->executed_hours,
                    'location_id' => $record->trainingEnvironment?->location_id,
                    'training_environment_id' => $record->training_environment_id,
                ];
            })



            ->before(function (array $data): void {
                //
            })


            ->action(function (array $data, $livewire, $record): void {
                if (! $record instanceof FichaCompetencyExecution) {
                    Notification::make()
                        ->title('Registro inválido')
                        ->danger()
                        ->send();

                    return;
                }

                $record->update([
                    'executed_hours' => $data['executed_hours'],
                    'training_environment_id' => $data['training_environment_id'] ?? null,
                ]);

                $livewire->dispatch('calendar-refresh');
            })
            ->extraModalFooterActions([
                Action::make('deleteExecution')
                    ->label('Eliminar')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->cancelParentActions()
                    ->requiresConfirmation()
                    ->modalHeading('¿Eliminar ejecución?')
                    ->modalDescription('Esta acción no se puede deshacer.')
                    ->action(function ($livewire, ?FichaCompetencyExecution $record): void {

                        if (! $record instanceof FichaCompetencyExecution) {
                            Notification::make()
                                ->title('No se pudo eliminar la ejecución')
                                ->danger()
                                ->body('El registro activo no es una ejecución válida.')
                                ->send();

                            return;
                        }

                        $record->delete();

                        $livewire->dispatch('calendar-refresh');


                        Notification::make()
                            ->title('Ejecución eliminada correctamente')
                            ->success()
                            ->send();
                    }),
            ])



            ->successNotificationTitle('Ejecución actualizada correctamente');
    }
}
