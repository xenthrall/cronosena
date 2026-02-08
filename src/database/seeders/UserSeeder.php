<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 🔹 Aseguramos que existan los roles base antes de asignarlos
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $planificadorRole = Role::firstOrCreate(['name' => 'usuario']);

        // 🔹 Usuario administrador
        $admin = User::firstOrCreate(
            ['email' => 'admin@cronosena.com'],
            [
                'name' => 'cronosena',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Asignar rol admin si no lo tiene aún
        if (! $admin->hasRole('admin')) {
            $admin->assignRole($adminRole);
        }

        // 🔹 Usuario planificación académica
        $planificador = User::firstOrCreate(
            ['email' => 'planificacion@cronosena.com'],
            [
                'name' => 'planificacion',
                'password' => Hash::make('password'),
            ]
        );

        if (! $planificador->hasRole('usuario')) {
            $planificador->assignRole($planificadorRole);
        }

    }
}
