<?php

namespace App\Filament\Resources\Fichas\Pages;

use App\Filament\Resources\Fichas\FichaResource;
use App\Models\Ficha;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class EditFicha extends EditRecord
{
    protected static string $resource = FichaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => Auth::user()?->can('ficha.delete'))
                ->disabled(function (Ficha $record) {
                    return $record->fichaCompetencies()->whereHas('executions')->exists();
                })
                ->tooltip(fn (Ficha $record) => $record->fichaCompetencies()->whereHas('executions')->exists() 
                    ? 'No se puede eliminar: Hay clases/ejecuciones registradas.' 
                    : null
                )
                ->before(function (Ficha $record, DeleteAction $action) {
                    if ($record->fichaCompetencies()->whereHas('executions')->exists()) {
                        Notification::make()
                            ->title('Acción Denegada')
                            ->body('No se puede eliminar la Ficha porque ya tiene ejecuciones (clases) registradas en sus competencias. Por seguridad, debe eliminar el historial de ejecuciones primero.')
                            ->danger()
                            ->duration(10000)
                            ->send();
                        $action->cancel();
                    }
                }),
        ];
    }
}