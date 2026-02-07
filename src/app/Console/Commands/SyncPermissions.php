<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class SyncPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cronosena:sync-permissions {--refresh : Limpia los permisos existentes antes de sincronizar}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza los permisos definidos en el sistema con la base de datos.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando sincronización de permisos...');

        if ($this->option('refresh')) {
            $this->info('Limpiando permisos existentes...');
            Permission::truncate();
            $this->info('Permisos existentes eliminados.');
        }

        $permissions = [
            'Usuarios' => [
                ['name' => 'user.view', 'action' => 'Ver Usuarios', 'description' => 'Permite ver la lista de usuarios.'],
                ['name' => 'user.create', 'action' => 'Crear Usuario', 'description' => 'Permite registrar nuevos usuarios en el sistema.'],
                ['name' => 'user.edit', 'action' => 'Editar Usuario', 'description' => 'Permite modificar la información de los usuarios existentes.'],
                ['name' => 'user.delete', 'action' => 'Eliminar Usuario', 'description' => 'Permite eliminar usuarios del sistema.'],
                ['name' => 'user.manageRoles', 'action' => 'Asignar Roles', 'description' => 'Permite asignar o modificar roles y permisos de los usuarios.'],
            ],

            'Roles y Permisos' => [
                ['name' => 'role.view', 'action' => 'Ver Roles', 'description' => 'Permite ver la lista de roles.'],
                ['name' => 'role.edit', 'action' => 'Editar Rol', 'description' => 'Permite modificar permisos de roles.'],
                ['name' => 'role.create', 'action' => 'Crear Rol', 'description' => 'Permite crear nuevos roles.'],
                ['name' => 'role.delete', 'action' => 'Eliminar Rol', 'description' => 'Permite eliminar roles existentes.'],
            ],

            'Instructores' => [
                // Permisos básicos
                ['name' => 'instructor.view', 'action' => 'Ver Instructores', 'description' => 'Permite visualizar la lista de instructores y sus detalles básicos.'],
                ['name' => 'instructor.create', 'action' => 'Crear Instructor', 'description' => 'Permite registrar nuevos instructores en el sistema.'],
                ['name' => 'instructor.edit', 'action' => 'Editar Instructor', 'description' => 'Permite modificar la información de un instructor existente.'],
                ['name' => 'instructor.delete', 'action' => 'Eliminar Instructor', 'description' => 'Permite eliminar un instructor del sistema.'],

                // Permisos funcionales
                ['name' => 'instructor.manageEquipoEjecutor', 'action' => 'Gestionar Equipo Ejecutor', 'description' => 'Permite asignar, modificar o eliminar la información del equipo ejecutor.'],
                ['name' => 'instructor.manageCompetencias', 'action' => 'Gestionar Competencias Vinculadas', 'description' => 'Permite vincular y administrar las competencias asociadas al instructor.'],
                ['name' => 'instructor.export', 'action' => 'Exportar Información de Instructores', 'description' => 'Permite exportar listados o fichas de instructores en formatos PDF o Excel.'],
            ],

            'Fichas' => [
                ['name' => 'ficha.view', 'action' => 'Ver Fichas', 'description' => 'Permite ver la lista de fichas.'],
                ['name' => 'ficha.create', 'action' => 'Crear Ficha', 'description' => 'Permite crear nuevas fichas.'],
                ['name' => 'ficha.edit', 'action' => 'Editar Ficha', 'description' => 'Permite modificar fichas existentes.'],
                ['name' => 'ficha.delete', 'action' => 'Eliminar Ficha', 'description' => 'Permite eliminar fichas del sistema.'],

                ['name' => 'ficha.manage', 'action' => 'Gestionar Ficha', 'description' => 'Permite gestionar detalles especificos de la ficha, como asignar instructores o competencias.'],
                ['name' => 'ficha.municipalities', 'action' => 'Gestionar Municipios', 'description' => 'Permite gestionar los municipios'],
                ['name' => 'ficha.shifts', 'action' => 'Gestionar Jornadas', 'description' => 'Permite gestionar las jornadas'],
            ],

            'Paneles' => [
                ['name' => 'panel.admin.access', 'action' => 'Acceso al Panel Administrativo', 'description' => 'Permite acceder y visualizar el panel administrativo del sistema.'],
                ['name' => 'panel.planificacion.access', 'action' => 'Acceso al Panel de Planificación', 'description' => 'Permite acceder y visualizar el panel de planificación del sistema.'],
                ['name' => 'panel.instructor.access', 'action' => 'Acceso al Panel de Instructor', 'description' => 'Permite acceder y visualizar el panel de instructor del sistema.'],
            ],

            'Reportes' => [
                ['name' => 'reportes.export', 'action' => 'Exportar Reportes', 'description' => 'Permite descargar reportes en PDF o Excel.'],
            ],
        ];

        foreach ($permissions as $group => $perms) {
            foreach ($perms as $perm) {
                Permission::firstOrCreate(
                    ['name' => $perm['name'], 'guard_name' => 'web'],
                    [
                        'group' => $group,
                        'action' => $perm['action'],
                        'description' => $perm['description'],
                    ]
                );
            }
        }

        $this->info('✅ Permisos sincronizados correctamente.');

        $this->createBaseRoles($permissions);

        return self::SUCCESS;
    }

    protected function createBaseRoles(array $permissions)
    {
        $roles = [
            'admin' => array_merge(...array_values($permissions)), // todos los permisos
            'usuario' => collect($permissions)
                ->flatten(1)
                ->filter(fn($perm) => str_contains($perm['name'], 'view'))
                ->values()
                ->toArray(),
        ];

        foreach ($roles as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions(collect($perms)->pluck('name')->toArray());
        }

        $this->info('🎯 Roles base creados y sincronizados: admin, usuario');
    }
}
