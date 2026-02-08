<?php

namespace App\Traits\Executions;

use Carbon\Carbon;

trait CalculatesExecutionHours
{
    /**
     * Calcula el total de horas entre dos fechas.
     */
    public static function calculateWorkHoursBetweenDates($startDate, $endDate)
    {
        if (!$startDate || !$endDate) {
            return null;
        }

        $start = Carbon::parse($startDate);
        $end   = Carbon::parse($endDate);

        if ($end->lessThan($start)) {
            return null;
        }

        $days = self::countDays($start, $end);
        $dailyHours   = 8;

        return $days * $dailyHours;
    }

    /**
     * Cuenta cuántos días existen entre dos fechas.
     */
    private static function countDays(Carbon $start, Carbon $end)
    {
        if ($end->lessThan($start)) {
            return 0;
        }

        return $start->copy()->startOfDay()
            ->diffInDays($end->copy()->startOfDay()) + 1;
            
    }

    /**
     * Calcula la cantidad máxima de horas ejecutables según las semanas
     * que cubre el rango de fechas y las horas restantes disponibles.
     * Cada semana permite hasta 48 horas.
     */
    public static function calculateMaxExecutableHours($startDate, $endDate, $remainingHours)
    {
        if (!$startDate || !$endDate) {
            return 0;
        }

        $start = Carbon::parse($startDate);
        $end   = Carbon::parse($endDate);

        if ($end->lessThan($start)) {
            return 0;
        }

        $weeksInRange = self::countDistinctIsoWeeks($start, $end);
        $maxHoursByWeeks = $weeksInRange * 48;

        return min($maxHoursByWeeks, $remainingHours);
    }

    /**
     * Cuenta las semanas ISO distintas cubiertas por un rango de fechas.
     */
    private static function countDistinctIsoWeeks(Carbon $start, Carbon $end)
    {
        $weeks = [];
        $current = $start->copy();

        while ($current->lte($end)) {
            $weekKey = $current->year . '-W' . $current->isoWeek();
            $weeks[$weekKey] = true;
            $current->addDay();
        }

        return count($weeks);
    }
}
