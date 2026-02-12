<?php

namespace App\Filament\Resources\Instructors\Pages;

use App\Filament\Resources\Instructors\InstructorResource;
use App\Models\Instructor; // Importar el modelo
use App\Services\ImageOptimizer;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification; // Importar Notificaciones
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EditInstructor extends EditRecord
{
    protected static string $resource = InstructorResource::class;

    public ?string $originalPhotoPath = null;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn() => Auth::user()?->can('instructor.delete'))

                ->disabled(function (Instructor $record) {
                    return $record->fichaCompetencyExecutions()->exists()
                        || $record->fichaInstructorLeaderships()->exists();
                })

                ->tooltip(function (Instructor $record) {
                    if ($record->fichaCompetencyExecutions()->exists()) {
                        return 'No se puede eliminar: El instructor tiene historial de clases impartidas.';
                    }
                    if ($record->fichaInstructorLeaderships()->exists()) {
                        return 'No se puede eliminar: El instructor figura como líder de ficha.';
                    }
                    return null;
                })

                ->before(function (Instructor $record, DeleteAction $action) {
                    $hasExecutions = $record->fichaCompetencyExecutions()->exists();
                    $hasLeadership = $record->fichaInstructorLeaderships()->exists();

                    if ($hasExecutions || $hasLeadership) {
                        Notification::make()
                            ->title('Acción Denegada')
                            ->body(
                                $hasExecutions
                                    ? 'El instructor tiene ejecuciones registradas. Por motivos de auditoría académica no se puede eliminar.'
                                    : 'El instructor está asignado como líder de una ficha activa o histórica.'
                            )
                            ->danger()
                            ->duration(10000)
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->originalPhotoPath = $this->getRecord()->photo_url;
        $newPath = $data['photo_url'];

        if ($this->originalPhotoPath === $newPath || !$newPath) {
            return $data;
        }

        try {
            $optimizer = app(ImageOptimizer::class);
            $optimizedPath = $optimizer->optimize($newPath, [
                'max_width' => 150,
                'quality' => 80,
                'delete_original' => true,
            ]);

            if ($optimizedPath) {
                $data['photo_url'] = $optimizedPath;
            }
        } catch (\Exception $e) {
            Log::error("Fallo al optimizar la nueva imagen para el instructor ID {$this->getRecord()->id}: " . $e->getMessage());
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $currentPhotoPath = $this->getRecord()->photo_url;

        if ($this->originalPhotoPath && $this->originalPhotoPath !== $currentPhotoPath) {
            Storage::disk('public')->delete($this->originalPhotoPath);
        }
    }
}
