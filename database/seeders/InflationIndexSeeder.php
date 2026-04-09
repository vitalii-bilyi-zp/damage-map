<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InflationIndex;

class InflationIndexSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $official = 'Держстат України, місячні індекси цін БМР до попереднього місяця, '
                  . 'кумулятивний коефіцієнт відносно 2024-01. '
                  . 'Джерело: index.minfin.com.ua/ua/economy/index/buildprice';

        $forecast = 'Прогноз на основі тренду 2024–2025 (~1.1% на місяць). '
                  . 'Підлягає оновленню після публікації офіційних даних Держстату.';

        $records = [
            // ── 2022 ──────────────────────────────────────────────────────────
            ['year' => 2022, 'month' => 1,  'index_value' => 0.7297, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2022, 'month' => 2,  'index_value' => 0.7758, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2022, 'month' => 3,  'index_value' => 0.7935, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2022, 'month' => 4,  'index_value' => 0.8063, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2022, 'month' => 5,  'index_value' => 0.8418, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2022, 'month' => 6,  'index_value' => 0.8812, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2022, 'month' => 7,  'index_value' => 0.8883, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2022, 'month' => 8,  'index_value' => 0.8972, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2022, 'month' => 9,  'index_value' => 0.9026, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2022, 'month' => 10, 'index_value' => 0.9134, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2022, 'month' => 11, 'index_value' => 0.9188, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2022, 'month' => 12, 'index_value' => 0.9262, 'source_type' => 'official', 'source_description' => $official],

            // ── 2023 ──────────────────────────────────────────────────────────
            ['year' => 2023, 'month' => 1,  'index_value' => 0.9308, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2023, 'month' => 2,  'index_value' => 0.9346, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2023, 'month' => 3,  'index_value' => 0.9412, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2023, 'month' => 4,  'index_value' => 0.9778, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2023, 'month' => 5,  'index_value' => 0.9903, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2023, 'month' => 6,  'index_value' => 0.9912, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2023, 'month' => 7,  'index_value' => 1.0031, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2023, 'month' => 8,  'index_value' => 1.0232, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2023, 'month' => 9,  'index_value' => 1.0253, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2023, 'month' => 10, 'index_value' => 1.0315, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2023, 'month' => 11, 'index_value' => 1.0264, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2023, 'month' => 12, 'index_value' => 1.0243, 'source_type' => 'official', 'source_description' => $official],

            // ── 2024 (базовий рік, місяць 1 = 1.0000) ─────────────────────────
            ['year' => 2024, 'month' => 1,  'index_value' => 1.0000, 'source_type' => 'official', 'source_description' => 'Базовий місяць навчання ML-моделі = 1.0000. Держстат України.'],
            ['year' => 2024, 'month' => 2,  'index_value' => 1.0131, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2024, 'month' => 3,  'index_value' => 1.0152, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2024, 'month' => 4,  'index_value' => 1.0223, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2024, 'month' => 5,  'index_value' => 1.0336, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2024, 'month' => 6,  'index_value' => 1.0398, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2024, 'month' => 7,  'index_value' => 1.0450, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2024, 'month' => 8,  'index_value' => 1.0481, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2024, 'month' => 9,  'index_value' => 1.0513, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2024, 'month' => 10, 'index_value' => 1.0502, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2024, 'month' => 11, 'index_value' => 1.0586, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2024, 'month' => 12, 'index_value' => 1.0628, 'source_type' => 'official', 'source_description' => $official],

            // ── 2025 (всі місяці завершені станом на квітень 2026) ─────────────
            ['year' => 2025, 'month' => 1,  'index_value' => 1.0713, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2025, 'month' => 2,  'index_value' => 1.0766, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2025, 'month' => 3,  'index_value' => 1.0842, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2025, 'month' => 4,  'index_value' => 1.0853, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2025, 'month' => 5,  'index_value' => 1.0885, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2025, 'month' => 6,  'index_value' => 1.0928, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2025, 'month' => 7,  'index_value' => 1.1016, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2025, 'month' => 8,  'index_value' => 1.1049, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2025, 'month' => 9,  'index_value' => 1.1060, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2025, 'month' => 10, 'index_value' => 1.1082, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2025, 'month' => 11, 'index_value' => 1.1159, 'source_type' => 'official', 'source_description' => $official],
            ['year' => 2025, 'month' => 12, 'index_value' => 1.1227, 'source_type' => 'official', 'source_description' => $official],

            // ── 2026 (прогноз) ────────────────────────────────────────────────
            ['year' => 2026, 'month' => 1,  'index_value' => 1.1352, 'source_type' => 'forecast', 'source_description' => $forecast],
            ['year' => 2026, 'month' => 2,  'index_value' => 1.1476, 'source_type' => 'forecast', 'source_description' => $forecast],
            ['year' => 2026, 'month' => 3,  'index_value' => 1.1603, 'source_type' => 'forecast', 'source_description' => $forecast],
            ['year' => 2026, 'month' => 4,  'index_value' => 1.1731, 'source_type' => 'forecast', 'source_description' => $forecast],
            ['year' => 2026, 'month' => 5,  'index_value' => 1.1861, 'source_type' => 'forecast', 'source_description' => $forecast],
            ['year' => 2026, 'month' => 6,  'index_value' => 1.1992, 'source_type' => 'forecast', 'source_description' => $forecast],
            ['year' => 2026, 'month' => 7,  'index_value' => 1.2126, 'source_type' => 'forecast', 'source_description' => $forecast],
            ['year' => 2026, 'month' => 8,  'index_value' => 1.2261, 'source_type' => 'forecast', 'source_description' => $forecast],
            ['year' => 2026, 'month' => 9,  'index_value' => 1.2398, 'source_type' => 'forecast', 'source_description' => $forecast],
            ['year' => 2026, 'month' => 10, 'index_value' => 1.2537, 'source_type' => 'forecast', 'source_description' => $forecast],
            ['year' => 2026, 'month' => 11, 'index_value' => 1.2678, 'source_type' => 'forecast', 'source_description' => $forecast],
            ['year' => 2026, 'month' => 12, 'index_value' => 1.2820, 'source_type' => 'forecast', 'source_description' => $forecast],

            // ── 2027 (прогноз) ────────────────────────────────────────────────
            ['year' => 2027, 'month' => 1,  'index_value' => 1.2964, 'source_type' => 'forecast', 'source_description' => $forecast],
            ['year' => 2027, 'month' => 2,  'index_value' => 1.3110, 'source_type' => 'forecast', 'source_description' => $forecast],
            ['year' => 2027, 'month' => 3,  'index_value' => 1.3258, 'source_type' => 'forecast', 'source_description' => $forecast],
            ['year' => 2027, 'month' => 4,  'index_value' => 1.3408, 'source_type' => 'forecast', 'source_description' => $forecast],
            ['year' => 2027, 'month' => 5,  'index_value' => 1.3560, 'source_type' => 'forecast', 'source_description' => $forecast],
            ['year' => 2027, 'month' => 6,  'index_value' => 1.3714, 'source_type' => 'forecast', 'source_description' => $forecast],
            ['year' => 2027, 'month' => 7,  'index_value' => 1.3870, 'source_type' => 'forecast', 'source_description' => $forecast],
            ['year' => 2027, 'month' => 8,  'index_value' => 1.4028, 'source_type' => 'forecast', 'source_description' => $forecast],
            ['year' => 2027, 'month' => 9,  'index_value' => 1.4188, 'source_type' => 'forecast', 'source_description' => $forecast],
            ['year' => 2027, 'month' => 10, 'index_value' => 1.4350, 'source_type' => 'forecast', 'source_description' => $forecast],
            ['year' => 2027, 'month' => 11, 'index_value' => 1.4515, 'source_type' => 'forecast', 'source_description' => $forecast],
            ['year' => 2027, 'month' => 12, 'index_value' => 1.4681, 'source_type' => 'forecast', 'source_description' => $forecast],
        ];

        foreach ($records as $record) {
            InflationIndex::updateOrCreate(
                ['year' => $record['year'], 'month' => $record['month']],
                [
                    'index_value'        => $record['index_value'],
                    'source_type'        => $record['source_type'],
                    'source_description' => $record['source_description'],
                ],
            );
        }
    }
}
