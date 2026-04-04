<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        SiteSetting::updateOrCreate(
            ['key' => 'registration_mode'],
            ['value' => 'closed']
        );
    }
}
