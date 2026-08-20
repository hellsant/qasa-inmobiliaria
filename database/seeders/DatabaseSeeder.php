<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            SettingSeeder::class,
            ZoneSeeder::class,
            MilestoneSeeder::class,
            FaqSeeder::class,
            TeamSeeder::class,
            TestimonialSeeder::class,
            PropertySeeder::class,
        ]);
    }
}