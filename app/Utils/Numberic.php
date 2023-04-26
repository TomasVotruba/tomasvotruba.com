<?php

declare(strict_types=1);

namespace App\Utils;

final class Numberic
{
    public static function stringToFloat(string $value): float
    {
        $numericValue = str_replace(['.', ','], ['', '.'], (string) $value);
        return (float) $numericValue;
    }
}
