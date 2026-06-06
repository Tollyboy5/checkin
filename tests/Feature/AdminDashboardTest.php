<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Shift;
use App\Models\Staff;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_requires_login(): void
    {
        $this->get('/admin')
            ->assertRedirect('/admin/login');
    }

    public function test_admin_can_login(): void
    {
        $this->createAdmin();

        $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ])->assertRedirect('/admin/dashboard');

        $this->assertAuthenticated();
    }

    public function test_admin_can_manage_staff(): void
    {
        $admin = $this->createAdmin();
        $shift = $this->createShift();

        $this->actingAs($admin)->post('/admin/staff', [
            'name' => 'Jordan Lee',
            'shift_id' => $shift->id,
            'pin' => '1357',
            'active' => '1',
        ])->assertRedirect('/admin/staff');

        $staff = Staff::firstWhere('name', 'Jordan Lee');

        $this->assertNotNull($staff);
        $this->assertTrue(Hash::check('1357', $staff->pin_hash));

        $this->actingAs($admin)->put("/admin/staff/{$staff->id}", [
            'name' => 'Jordan Lane',
            'shift_id' => $shift->id,
            'pin' => '',
            'active' => '0',
        ])->assertRedirect('/admin/staff');

        $this->assertDatabaseHas('staff', [
            'id' => $staff->id,
            'name' => 'Jordan Lane',
            'active' => false,
        ]);
    }

    public function test_admin_can_manage_shifts(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)->post('/admin/shifts', [
            'name' => 'Evening Shift',
            'starts_at' => '13:00',
            'ends_at' => '21:00',
            'grace_minutes' => '10',
            'active' => '1',
        ])->assertRedirect('/admin/shifts');

        $shift = Shift::firstWhere('name', 'Evening Shift');

        $this->actingAs($admin)->put("/admin/shifts/{$shift->id}", [
            'name' => 'Late Shift',
            'starts_at' => '14:00',
            'ends_at' => '22:00',
            'grace_minutes' => '15',
            'active' => '1',
        ])->assertRedirect('/admin/shifts');

        $this->assertDatabaseHas('shifts', [
            'id' => $shift->id,
            'name' => 'Late Shift',
            'grace_minutes' => 15,
        ]);
    }

    public function test_dashboard_shows_daily_late_absent_and_grades(): void
    {
        $admin = $this->createAdmin();
        $shift = $this->createShift();
        $punctual = $this->createStaff('Punctual Person', $shift);
        $late = $this->createStaff('Late Person', $shift);
        $absent = $this->createStaff('Absent Person', $shift);
        $date = Carbon::parse('2026-06-02');

        Attendance::create([
            'staff_id' => $punctual->id,
            'work_date' => $date,
            'checked_in_at' => Carbon::parse('2026-06-02 09:02:00'),
            'check_in_ip' => '127.0.0.1',
        ]);

        Attendance::create([
            'staff_id' => $late->id,
            'work_date' => $date,
            'checked_in_at' => Carbon::parse('2026-06-02 09:08:00'),
            'check_in_ip' => '127.0.0.1',
        ]);

        $this->actingAs($admin)->get('/admin/dashboard?date=2026-06-02')
            ->assertOk()
            ->assertSee('Daily Attendance')
            ->assertSee('Late Staff')
            ->assertSee('Absent Staff')
            ->assertSee('Punctual Person')
            ->assertSee('Late Person')
            ->assertSee('Absent Person')
            ->assertSee('A')
            ->assertSee('C')
            ->assertSee('F');

        $this->assertNotNull($absent);
    }

    public function test_admin_can_edit_attendance_records(): void
    {
        $admin = $this->createAdmin();
        $shift = $this->createShift();
        $staff = $this->createStaff('Avery Johnson', $shift);
        $attendance = Attendance::create([
            'staff_id' => $staff->id,
            'work_date' => Carbon::parse('2026-06-02'),
            'checked_in_at' => Carbon::parse('2026-06-02 09:00:00'),
            'check_in_ip' => '127.0.0.1',
        ]);

        $this->actingAs($admin)->put("/admin/attendance/{$attendance->id}", [
            'staff_id' => $staff->id,
            'work_date' => '2026-06-02',
            'checked_in_at' => '09:03',
            'checked_out_at' => '17:05',
            'check_in_ip' => '127.0.0.2',
        ])->assertRedirect('/admin/attendance?date=2026-06-02');

        $attendance->refresh();

        $this->assertSame('09:03', $attendance->checked_in_at->format('H:i'));
        $this->assertSame('17:05', $attendance->checked_out_at->format('H:i'));
        $this->assertSame('127.0.0.2', $attendance->check_in_ip);
    }

    public function test_admin_pages_show_completed_overnight_attendance_on_checkout_day(): void
    {
        $admin = $this->createAdmin();
        $shift = $this->createShift();
        $staff = $this->createStaff('Night Person', $shift);

        Attendance::create([
            'staff_id' => $staff->id,
            'work_date' => Carbon::parse('2026-06-02'),
            'checked_in_at' => Carbon::parse('2026-06-02 23:00:00'),
            'checked_out_at' => Carbon::parse('2026-06-03 08:00:00'),
            'check_in_ip' => '127.0.0.1',
        ]);

        $this->actingAs($admin)->get('/admin/attendance?date=2026-06-03')
            ->assertOk()
            ->assertSee('Night Person')
            ->assertSee('11:00 PM')
            ->assertSee('8:00 AM')
            ->assertSee('9h 0m');

        $this->actingAs($admin)->get('/admin/dashboard?date=2026-06-03')
            ->assertOk()
            ->assertSee('Night Person')
            ->assertSee('11:00 PM')
            ->assertSee('8:00 AM')
            ->assertSee('9h 0m');
    }

    public function test_admin_can_manage_rosters(): void
    {
        $admin = $this->createAdmin();
        $shift = $this->createShift();
        $staff = $this->createStaff('Anthony', $shift);

        $this->actingAs($admin)->post('/admin/rosters', [
            'staff_id' => $staff->id,
            'shift_id' => $shift->id,
            'roster_date' => '2026-06-02',
            'notes' => 'Front desk',
        ])->assertRedirect('/admin/rosters?date=2026-06-02');

        $this->assertDatabaseHas('rosters', [
            'staff_id' => $staff->id,
            'shift_id' => $shift->id,
            'roster_date' => '2026-06-02 00:00:00',
            'notes' => 'Front desk',
        ]);
    }

    private function createAdmin(): User
    {
        return User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
    }

    private function createShift(): Shift
    {
        return Shift::create([
            'name' => 'Morning Shift',
            'starts_at' => '09:00',
            'ends_at' => '17:00',
            'grace_minutes' => 5,
            'active' => true,
        ]);
    }

    private function createStaff(string $name, Shift $shift): Staff
    {
        return Staff::create([
            'name' => $name,
            'shift_id' => $shift->id,
            'pin_hash' => Hash::make('1234'),
            'active' => true,
        ]);
    }
}
