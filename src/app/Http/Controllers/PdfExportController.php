<?php

namespace App\Http\Controllers;

use App\Traits\Gantt\GanttBarsTrait;
use Illuminate\Support\Carbon;
use App\Models\FichaCompetencyExecution;
use App\Models\Instructor;
use App\Traits\Gantt\HasGanttConfiguration;
use Illuminate\Support\Collection;





class PdfExportController extends Controller
{

    use GanttBarsTrait;
    use HasGanttConfiguration;

    public function configure(): static
    {
        return $this
            ->entityName('Instructor')
            ->columnsWidth(40)
            ->rowHeight(54);
    }

    public ?string $fichaId = null;

    public string $monthLabel;

    public int $totalColumns = 0;

    public array $columns = [];

    public array $rows = [];
    public array $barsByRow = [];

    public function fetchRecords(Carbon $periodStart, Carbon $periodEnd)
    {
        return FichaCompetencyExecution::query()
            ->with(['fichaCompetency.ficha', 'fichaCompetency.competency', 'instructor'])
            ->when(
                $this->fichaId,
                fn($q) =>
                $q->whereHas('fichaCompetency', fn($sq) => $sq->where('ficha_id', $this->fichaId))
            )
            ->where(function ($q) use ($periodStart, $periodEnd) {
                $q->whereBetween('execution_date', [$periodStart, $periodEnd])
                    ->orWhere(function ($q2) use ($periodStart, $periodEnd) {
                        $q2->whereNotNull('completion_date')
                            ->where('execution_date', '<=', $periodEnd)
                            ->where('completion_date', '>=', $periodStart);
                    })
                    ->orWhere(function ($q3) use ($periodEnd) {
                        $q3->whereNull('completion_date')
                            ->where('execution_date', '<=', $periodEnd);
                    });
            })
            ->get();
    }

    public function buildBars(Collection|array $records, Carbon $periodStart, Carbon $periodEnd): void
    {
        $instructores = Instructor::query()
            ->whereIn('id', $records->pluck('instructor_id')->unique())
            ->orderBy('full_name')
            ->get();

        // Filas
        foreach ($instructores as $instructor) {
            $this->rows[] = [
                'id'        => $instructor->id,
                'label'     => $instructor->full_name,
                'avatarUrl' => $instructor->getFilamentAvatarUrl(),
            ];
        }

        // Barras
        foreach ($records as $exec) {

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

            $this->barsByRow[$exec->instructor_id][] = $bar;
        }
    }

    public function exportMonthlyExecutionsReport($month, $year)
    {
        $monthLabel = Carbon::create($year, $month, 1)->translatedFormat('F');

        $periodStart = Carbon::create($year, $month, 1)->startOfDay();
        $periodEnd = $periodStart->clone()->endOfMonth()->endOfDay();
        $this->columns = collect(range(0, $periodStart->diffInDays($periodEnd)))
            ->map(fn($d) => $periodStart->clone()->addDays($d))
            ->toArray();

        $this->totalColumns = count($this->columns);
        $records = $this->fetchRecords($periodStart, $periodEnd);
        $this->buildBars($records, $periodStart, $periodEnd);

        $this->configure();

        return view('exports.reports.monthly-executions', [
            'month'         => $monthLabel,
            'year'          => $year,
            'entityName'    => $this->entityName,
            'totalDays'     => $this->totalColumns,
            'dayWidthPx'    => $this->columnsWidthPx,
            'rowHeightPx'   => $this->rowHeightPx,
            'days'       => $this->columns,
            'rows'          => $this->rows,
            'barsByRow'     => $this->barsByRow
        ]);
    }

}
