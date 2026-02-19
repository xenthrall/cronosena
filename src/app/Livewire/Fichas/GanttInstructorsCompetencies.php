<?php

namespace App\Livewire\Fichas;

use App\Livewire\Base\GanttBaseComponent;
use App\Models\Instructor;
use App\Models\FichaCompetencyExecution;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class GanttInstructorsCompetencies extends GanttBaseComponent
{
    public ?string $fichaId = null;

    public function configure(): static
    {
        return $this
            ->entityName('Instructor')
            ->columnsWidth(50)
            ->rowHeight(70);
    }

    /**
     * Obtiene las ejecuciones de competencias de los instructores en el rango del mes.
     */
    protected function fetchRecords(Carbon $periodStart, Carbon $periodEnd): Collection
    {
        return FichaCompetencyExecution::query()
            ->with(['fichaCompetency.ficha', 'fichaCompetency.competency', 'instructor'])
            ->when(
                $this->fichaId,
                fn ($q) => $q->whereHas(
                    'fichaCompetency',
                    fn ($sq) => $sq->where('ficha_id', $this->fichaId)
                )
            )
            ->where(function ($q) use ($periodStart, $periodEnd) {

                // ejecución dentro del mes
                $q->whereBetween('execution_date', [$periodStart, $periodEnd])

                    // ejecución que comenzó antes y terminó dentro o después
                    ->orWhere(function ($q2) use ($periodStart, $periodEnd) {
                        $q2->whereNotNull('completion_date')
                            ->where('execution_date', '<=', $periodEnd)
                            ->where('completion_date', '>=', $periodStart);
                    })

                    // ejecución sin fecha fin pero activa antes de periodEnd
                    ->orWhere(function ($q3) use ($periodEnd) {
                        $q3->whereNull('completion_date')
                            ->where('execution_date', '<=', $periodEnd);
                    });
            })
            ->get();
    }

    /**
     * Procesa los registros y construye las barras agrupadas por instructor.
     */
    protected function buildBars(Collection|array $records, Carbon $periodStart, Carbon $periodEnd): void
    {
        // Solo usar instructores presentes en los records
        $instructorIds = $records->pluck('instructor_id')->unique();

        $instructors = Instructor::whereIn('id', $instructorIds)
            ->orderBy('last_name')
            ->get();

        $this->rows = [];
        $this->barsByRow = [];

        foreach ($instructors as $instructor) {

            // Filtrar ejecuciones únicamente del instructor actual
            $executions = $records->where('instructor_id', $instructor->id);

            // Si no tiene ejecuciones válidas → no se incluye
            if ($executions->isEmpty()) {
                continue;
            }

            // Crear fila
            $this->rows[] = [
                'id'        => $instructor->id,
                'label'     => $instructor->full_name,
                'sub_label' => $instructor->institutional_email,
                'avatarUrl' => $instructor->user?->getFilamentAvatarUrl(),
            ];

            // Inicializar contenedor de barras
            $this->barsByRow[$instructor->id] = [];

            foreach ($executions as $exec) {
                $execStart = Carbon::parse($exec->execution_date)->startOfDay();
                $execEnd = $exec->completion_date
                    ? Carbon::parse($exec->completion_date)->endOfDay()
                    : $execStart;

                $bar = $this->makeGanttBar(
                    meta: [
                        'label'     => $exec->fichaCompetency->competency->name ?? 'Competencia',
                        'sub_label' => $exec->fichaCompetency->ficha->code ?? 'N/A',
                        'badge'     => $exec->executed_hours . 'h',
                    ],
                    execStart: $execStart,
                    execEnd: $execEnd,
                    periodStart: $periodStart,
                    periodEnd: $periodEnd,
                    totalColumns: $this->totalColumns
                );

                if ($bar) {
                    $this->barsByRow[$instructor->id][] = $bar;
                }
            }

            // Si no se generó ninguna barra final → eliminar instructor
            if (empty($this->barsByRow[$instructor->id])) {
                unset($this->barsByRow[$instructor->id]);
                array_pop($this->rows); // remover última fila agregada
            }
        }
    }
}
