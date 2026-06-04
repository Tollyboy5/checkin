<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class DayShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shift::updateOrCreate(
            ['name' => 'Day Shift'],
            [
                'starts_at' => '09:00',
                'ends_at' => '17:00',
                'grace_minutes' => 5,
                'active' => true,
            ]
        );
    }
}
