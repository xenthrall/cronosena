<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use App\Models\Role;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected array $permissionsToSync = [];

    protected function getHeaderActions(): array
    {
        $actions = [];

        // Solo mostrar el botón "Eliminar" si el usuario tiene permiso
        if (Auth::user()?->can('role.delete')) {
            $actions[] = DeleteAction::make()
                ->disabled(function (Role $record) {
                    return $record->users()->exists();
                })
                ->tooltip(function (Role $record) {
                    if ($record->users()->exists()) {
                        return 'No se puede eliminar porque hay usuarios usando este rol';
                    }
                    return null;
                })
                ->before(function (Role $record, DeleteAction $action) {
                    if ($record->users()->exists()) {
                        Notification::make()
                            ->title('No se puede eliminar el rol')
                            ->body('Este rol está asignado a uno o más usuarios en el sistema. Debe quitarle este rol a esos usuarios antes de poder eliminarlo.')
                            ->danger()
                            ->duration(8000)
                            ->send();

                        $action->cancel();
                    }
                });
        }

        return $actions;
    }


    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Extraer IDs de permisos desde el array anidado
        $nested = $data['permissions'] ?? [];

        $this->permissionsToSync = collect($nested)
            ->flatMap(fn($arr) => is_array($arr) ? $arr : [])
            ->filter()
            ->values()
            ->all();

        unset($data['permissions']);

        return $data;
    }

    protected function afterSave(): void
    {
        if (! empty($this->permissionsToSync)) {
            $permissions = Permission::whereIn('id', $this->permissionsToSync)->get();
            $this->record->syncPermissions($permissions);
        } else {
            $this->record->syncPermissions([]); // Limpia todos si no hay seleccionados
        }
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Esto rellena los permisos actuales 
        $ids = $this->record->permissions()->pluck('id')->toArray();

        $grouped = Permission::whereIn('id', $ids)
            ->get()
            ->groupBy('group')
            ->map(fn($items) => $items->pluck('id')->toArray())
            ->toArray();

        $data['permissions'] = $grouped;

        return $data;
    }
}
