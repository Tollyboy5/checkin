<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class NightShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shift::updateOrCreate(
            ['name' => 'Night Shift'],
            [
                'starts_at' => '21:00',
                'ends_at' => '05:00',
                'grace_minutes' => 10,
                'active' => true,
            ]
        );
    }
}
