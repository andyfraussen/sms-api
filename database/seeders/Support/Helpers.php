<?php

namespace Database\Seeders\Support;

class Helpers
{
    public static function currentAcademicYear(): string
    {
        $year = now()->month >= 9 ? now()->year : now()->year - 1;

        return $year . '-' . ($year + 1);
    }
}

