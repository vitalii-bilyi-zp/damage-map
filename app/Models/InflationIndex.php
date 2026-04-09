<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InflationIndex extends Model
{
    protected $fillable = [
        'year',
        'month',
        'index_value',
        'source_type',
        'source_description'
    ];

    protected $casts = [
        'year'        => 'integer',
        'month'       => 'integer',
        'index_value' => 'float',
    ];

    public static function getCoefficientForPeriod(int $year, int $month): float
    {
        return (float) (static::where('year', $year)
            ->where('month', $month)
            ->value('index_value') ?? 1.0);
    }

    public static function getAllForPayload(): array
    {
        return \Illuminate\Support\Facades\Cache::remember(
            'inflation_indices_payload',
            3600,
            fn() => static::orderBy('year')
                ->orderBy('month')
                ->get(['year', 'month', 'index_value'])
                ->map(fn($row) => [
                    'year'        => $row->year,
                    'month'       => $row->month,
                    'index_value' => (float) $row->index_value,
                ])
                ->values()
                ->toArray()
        );
    }
}
