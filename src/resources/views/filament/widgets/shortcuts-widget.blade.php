<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100">
                Accesos rápidos
            </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Programación instructores -->
            <a
                href="{{ route('filament.admin.resources.programacion-instructors.index') }}"
                class="group flex items-center gap-4 p-4 rounded-xl border
                       bg-white dark:bg-gray-900
                       border-gray-200 dark:border-gray-700
                       hover:bg-gray-50 dark:hover:bg-gray-800
                       transition"
            >
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-lg
                           bg-primary-50 dark:bg-primary-500/10
                           text-primary-600 dark:text-primary-400"
                >
                    <x-heroicon-o-calendar-days class="h-6 w-6" />
                </div>

                <div class="flex-1">
                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        Programación de instructores
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        Asignación y control de horarios
                    </div>
                </div>

                <x-heroicon-o-chevron-right
                    class="h-5 w-5 text-gray-400 dark:text-gray-500
                           group-hover:text-gray-600 dark:group-hover:text-gray-300"
                />
            </a>

            <!-- Gestionar fichas -->
            <a
                href="{{ route('filament.admin.resources.fichas.index') }}"
                class="group flex items-center gap-4 p-4 rounded-xl border
                       bg-white dark:bg-gray-900
                       border-gray-200 dark:border-gray-700
                       hover:bg-gray-50 dark:hover:bg-gray-800
                       transition"
            >
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-lg
                           bg-primary-50 dark:bg-primary-500/10
                           text-primary-600 dark:text-primary-400"
                >
                    <x-heroicon-o-rectangle-stack class="h-6 w-6" />
                </div>

                <div class="flex-1">
                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        Gestionar fichas
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        Administración de fichas académicas
                    </div>
                </div>

                <x-heroicon-o-chevron-right
                    class="h-5 w-5 text-gray-400 dark:text-gray-500
                           group-hover:text-gray-600 dark:group-hover:text-gray-300"
                />
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
