<?php

namespace Database\Seeders;

use App\Models\Conference;
use Illuminate\Database\Seeder;

class ConferenceSeeder extends Seeder
{
    public function run(): void
    {
        Conference::create([
            'title' => 'AWS Summit 2026',
            'slug' => 'aws-summit-2026',
            'conference_date' => '2026-08-20',
            'active_start_date' => '2026-08-01',
            'active_end_date' => '2026-08-15',
            'venue_name' => 'Noida Expo Center',
            'venue_address' => 'Sector 16, Noida',
            'venue_city' => 'Noida',
            'venue_state' => 'Uttar Pradesh',
            'google_map_url' => 'https://google.com/maps/place/Noida+Expo+Center',
            'status' => 'active',
        ]);
    }
}