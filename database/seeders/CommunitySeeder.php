<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;
use App\Models\District;
use App\Models\Community;
use File;

class CommunitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get('database/data/communities.json');
        $communities = json_decode($json);

        foreach ($communities as $value) {
            $region = Region::firstOrCreate([
                'name' => $value->region,
            ]);

            $district = District::firstOrCreate(
                ['name' => $value->district],
                ['region_id' => $region->id]
            );

            Community::updateOrCreate(
                ['name' => $value->community],
                ['district_id' => $district->id]
            );
        }
    }
}
