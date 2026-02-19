<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\FichaCompetencyExecution;
use Carbon\Carbon;


class HorarioApiController extends Controller
{
    public function eventsInstructor($id)
    {
        $events = FichaCompetencyExecution::where('instructor_id', $id)
            ->with([
                'fichaCompetency.ficha.shift',
                'fichaCompetency.competency',
                'instructor',
                'trainingEnvironment.location'
            ])
            ->get()
            ->map(function ($execution) {

                $shift = $execution->fichaCompetency?->ficha?->shift;

                $startDate = Carbon::parse($execution->execution_date);
                $endDate   = Carbon::parse($execution->completion_date);

                /*
                |--------------------------------------------------------------------------
                | Rango de ejecución legible (UX-friendly)
                |--------------------------------------------------------------------------
                */
                $executionRange = match (true) {
                    // Mismo día
                    $startDate->isSameDay($endDate) =>
                    $startDate->translatedFormat('j \\d\\e F'),

                    // Mismo mes y año
                    $startDate->isSameMonth($endDate) =>
                    $startDate->translatedFormat('j') . '–' .
                        $endDate->translatedFormat('j \\d\\e F'),

                    // Mismo año
                    $startDate->isSameYear($endDate) =>
                    $startDate->translatedFormat('j \\d\\e F') . ' – ' .
                        $endDate->translatedFormat('j \\d\\e F'),

                    // Diferente año
                    default =>
                    $startDate->translatedFormat('j \\d\\e F Y') . ' – ' .
                        $endDate->translatedFormat('j \\d\\e F Y'),
                };

                return [
                    'title' => sprintf(
                        'Ficha %s - %s',
                        $execution->fichaCompetency->ficha->code,
                        $execution->fichaCompetency->competency->name ?? ''
                    ),

                    // Fechas reales (FullCalendar)
                    'start' => $startDate,
                    'end'   => $endDate->copy()->addDay(),
                    'allDay' => true,

                    'color' => $shift?->color ?? '#47d837',

                    // Datos de dominio
                    'extendedProps' => [
                        'execution_id'     => $execution->id,
                        'shift'            => $shift?->name ?? 'Sin jornada',
                        'execution_range'  => $executionRange,
                        'instructor'       => $execution->instructor->full_name ?? null,
                        'executed_hours'   => $execution->executed_hours,
                        'location'         => $execution->trainingEnvironment->location->name ?? null,
                        'environment'      => $execution->trainingEnvironment->name ?? null,
                    ],
                ];
            });

        return response()->json($events);
    }

    public function eventsFicha($id)
    {
        $events = FichaCompetencyExecution::whereHas('fichaCompetency.ficha', function ($query) use ($id) {
            $query->where('id', $id);
        })
            ->with([
                'fichaCompetency.ficha.shift',
                'fichaCompetency.competency',
                'instructor',
                'trainingEnvironment.location'
            ])
            ->get()
            ->map(function ($execution) {

                $shift = $execution->fichaCompetency?->ficha?->shift;

                $startDate = Carbon::parse($execution->execution_date);
                $endDate   = Carbon::parse($execution->completion_date);

                /*
            |--------------------------------------------------------------------------
            | Rango de ejecución legible (UX-friendly)
            |--------------------------------------------------------------------------
            */
                $executionRange = match (true) {
                    // Mismo día
                    $startDate->isSameDay($endDate) =>
                    $startDate->translatedFormat('j \\d\\e F'),

                    // Mismo mes y año
                    $startDate->isSameMonth($endDate) =>
                    $startDate->translatedFormat('j') . '–' .
                        $endDate->translatedFormat('j \\d\\e F'),

                    // Mismo año
                    $startDate->isSameYear($endDate) =>
                    $startDate->translatedFormat('j \\d\\e F') . ' – ' .
                        $endDate->translatedFormat('j \\d\\e F'),

                    // Diferente año
                    default =>
                    $startDate->translatedFormat('j \\d\\e F Y') . ' – ' .
                        $endDate->translatedFormat('j \\d\\e F Y'),
                };

                return [
                    'title' => sprintf(
                        'Instructor %s - %s',
                        $execution->instructor->name ?? '',
                        $execution->fichaCompetency->competency->name ?? ''
                    ),

                    // Fechas reales (FullCalendar)
                    'start'  => $startDate,
                    'end'    => $endDate->copy()->addDay(),
                    'allDay' => true,

                    'color' => $shift?->color ?? '#47d837',

                    // Datos de dominio
                    'extendedProps' => [
                        'execution_id'    => $execution->id,
                        'shift'           => $shift?->name ?? 'Sin jornada',
                        'execution_range' => $executionRange,
                        'instructor'      => $execution->instructor->full_name ?? null,
                        'executed_hours'  => $execution->executed_hours,
                        'location'        => $execution->trainingEnvironment->location->name ?? null,
                        'environment'     => $execution->trainingEnvironment->name ?? null,
                    ],
                ];
            });

        return response()->json($events);
    }


    public function eventsAmbiente($id)
    {
        $events = FichaCompetencyExecution::where('training_environment_id', $id)
            ->with([
                'fichaCompetency.ficha.shift',
                'fichaCompetency.competency',
                'instructor',
                'trainingEnvironment.location'
            ])
            ->get()
            ->map(function ($execution) {

                $shift = $execution->fichaCompetency?->ficha?->shift;

                $startDate = Carbon::parse($execution->execution_date);
                $endDate   = Carbon::parse($execution->completion_date);

                /*
            |--------------------------------------------------------------------------
            | Rango de ejecución legible (UX-friendly)
            |--------------------------------------------------------------------------
            */
                $executionRange = match (true) {
                    // Mismo día
                    $startDate->isSameDay($endDate) =>
                    $startDate->translatedFormat('j \\d\\e F'),

                    // Mismo mes y año
                    $startDate->isSameMonth($endDate) =>
                    $startDate->translatedFormat('j') . '–' .
                        $endDate->translatedFormat('j \\d\\e F'),

                    // Mismo año
                    $startDate->isSameYear($endDate) =>
                    $startDate->translatedFormat('j \\d\\e F') . ' – ' .
                        $endDate->translatedFormat('j \\d\\e F'),

                    // Diferente año
                    default =>
                    $startDate->translatedFormat('j \\d\\e F Y') . ' – ' .
                        $endDate->translatedFormat('j \\d\\e F Y'),
                };

                return [
                    'title' => sprintf(
                        'Ficha %s - %s',
                        $execution->fichaCompetency->ficha->code ?? '',
                        $execution->fichaCompetency->competency->name ?? ''
                    ),

                    // Fechas reales (FullCalendar)
                    'start'  => $startDate,
                    'end'    => $endDate->copy()->addDay(),
                    'allDay' => true,

                    'color' => $shift?->color ?? '#47d837',

                    // Datos de dominio
                    'extendedProps' => [
                        'execution_id'    => $execution->id,
                        'shift'           => $shift?->name ?? 'Sin jornada',
                        'execution_range' => $executionRange,
                        'instructor'      => $execution->instructor->full_name ?? null,
                        'executed_hours'  => $execution->executed_hours,
                        'location'        => $execution->trainingEnvironment->location->name ?? null,
                        'environment'     => $execution->trainingEnvironment->name ?? null,
                    ],
                ];
            });

        return response()->json($events);
    }
}
