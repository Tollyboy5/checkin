<?php

namespace Database\Seeders;

use App\Models\Shift;
use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dayShift = Shift::firstWhere('name', 'Day Shift');

        collect([
            'Anthony',
            'Israel Abioye Wale',
            'Marvellous',
            'Tobi',
            'Ayo',
            'Williams',
            'Monsuno',
        ])->each(function (string $name) use ($dayShift) {
            Staff::updateOrCreate(
                ['name' => $name],
                [
                    'shift_id' => $dayShift?->id,
                    'pin_hash' => Hash::make('1234'),
                    'active' => true,
                ]
            );
        });
    }
}
