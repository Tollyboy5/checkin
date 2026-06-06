<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_attendance_page_loads_without_login(): void
    {
        $this->createStaff();

        $this->get('/')
            ->assertOk()
            ->assertSee('Staff Attendance')
            ->assertSee('Anthony');
    }

    public function test_staff_can_check_in_and_ip_address_is_recorded(): void
    {
        $staff = $this->createStaff();

        $this->post('/check-in', [
            'staff_id' => $staff->id,
            'pin' => '1234',
        ], ['REMOTE_ADDR' => '127.0.0.1'])
            ->assertRedirect('/')
            ->assertSessionHas('success', 'Check-in recorded.');

        $this->assertDatabaseHas('attendances', [
            'staff_id' => $staff->id,
            'work_date' => today()->format('Y-m-d H:i:s'),
            'check_in_ip' => '127.0.0.1',
        ]);
    }

    public function test_public_check_in_does_not_require_a_csrf_session_token(): void
    {
        $staff = $this->createStaff();

        $this->withMiddleware()->post('/check-in', [
            'staff_id' => $staff->id,
            'pin' => '1234',
        ])->assertRedirect('/');

        $this->assertDatabaseHas('attendances', [
            'staff_id' => $staff->id,
        ]);
    }

    public function test_duplicate_check_in_is_prevented_for_same_day(): void
    {
        $staff = $this->createStaff();

        Attendance::create([
            'staff_id' => $staff->id,
            'work_date' => today(),
            'checked_in_at' => now(),
            'check_in_ip' => '127.0.0.1',
        ]);

        $this->post('/check-in', [
            'staff_id' => $staff->id,
            'pin' => '1234',
        ])->assertSessionHas('error', 'You have already checked in today.');

        $this->assertSame(1, Attendance::count());
    }

    public function test_checkout_before_check_in_is_prevented(): void
    {
        $staff = $this->createStaff();

        $this->post('/check-out', [
            'staff_id' => $staff->id,
            'pin' => '1234',
        ])->assertSessionHas('error', 'You need to check in before checking out.');

        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_staff_can_check_out_once(): void
    {
        $staff = $this->createStaff();

        Attendance::create([
            'staff_id' => $staff->id,
            'work_date' => today(),
            'checked_in_at' => now(),
            'check_in_ip' => '127.0.0.1',
        ]);

        $this->post('/check-out', [
            'staff_id' => $staff->id,
            'pin' => '1234',
        ])->assertSessionHas('success', 'Check-out recorded.');

        $this->assertNotNull(Attendance::first()->checked_out_at);

        $this->post('/check-out', [
            'staff_id' => $staff->id,
            'pin' => '1234',
        ])->assertSessionHas('error', 'You have already checked out today.');
    }

    public function test_staff_can_check_out_open_attendance_from_previous_day(): void
    {
        $staff = $this->createStaff();

        $this->travelTo(now()->subDay()->setTime(23, 0));

        $attendance = Attendance::create([
            'staff_id' => $staff->id,
            'work_date' => today(),
            'checked_in_at' => now(),
            'check_in_ip' => '127.0.0.1',
        ]);

        $this->travelTo(now()->addDay()->setTime(8, 0));

        $this->post('/check-out', [
            'staff_id' => $staff->id,
            'pin' => '1234',
        ])->assertSessionHas('success', 'Check-out recorded.');

        $this->assertSame(1, Attendance::count());
        $this->assertSame('08:00', $attendance->fresh()->checked_out_at->format('H:i'));
    }

    public function test_public_page_shows_completed_overnight_attendance_on_checkout_day(): void
    {
        $staff = $this->createStaff();

        $attendance = Attendance::create([
            'staff_id' => $staff->id,
            'work_date' => now()->subDay()->startOfDay(),
            'checked_in_at' => now()->subDay()->setTime(23, 0),
            'checked_out_at' => now()->setTime(8, 0),
            'check_in_ip' => '127.0.0.1',
        ]);

        $this->travelTo($attendance->checked_out_at);

        $this->get('/')
            ->assertOk()
            ->assertSee('Checked out')
            ->assertSee('11:00 PM')
            ->assertSee('8:00 AM')
            ->assertSee('9h 0m');
    }

    public function test_public_page_shows_open_attendance_from_previous_day(): void
    {
        $staff = $this->createStaff();

        $this->travelTo(now()->subDay()->setTime(23, 0));

        Attendance::create([
            'staff_id' => $staff->id,
            'work_date' => today(),
            'checked_in_at' => now(),
            'check_in_ip' => '127.0.0.1',
        ]);

        $this->travelTo(now()->addDay()->setTime(8, 0));

        $this->get('/')
            ->assertOk()
            ->assertSee('Checked in')
            ->assertSee('11:00 PM');
    }

    public function test_incorrect_pin_is_rejected(): void
    {
        $staff = $this->createStaff();

        $this->post('/check-in', [
            'staff_id' => $staff->id,
            'pin' => '0000',
        ])->assertSessionHasErrors('pin');

        $this->assertDatabaseCount('attendances', 0);
    }

    private function createStaff(): Staff
    {
        return Staff::create([
            'name' => 'Anthony',
            'pin_hash' => Hash::make('1234'),
            'active' => true,
        ]);
    }
}
