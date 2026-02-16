<?php

namespace App\Filament\Resources\Programs\Pages;

use App\Filament\Resources\Programs\ProgramResource;
use App\Models\Program;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProgram extends EditRecord
{
    protected static string $resource = ProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),

            DeleteAction::make()
                ->disabled(function (Program $record) {
                    return $record->competencies()->exists();
                })
                ->tooltip('No se puede eliminar porque tiene competencias asociadas')
                ->before(function (Program $record, DeleteAction $action) {
                    if ($record->competencies()->exists()) {
                        Notification::make()
                            ->title('No se puede eliminar el programa')
                            ->body('Este programa tiene competencias asociadas. Elimínelas primero o archive el programa.')
                            ->danger()
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }
}
