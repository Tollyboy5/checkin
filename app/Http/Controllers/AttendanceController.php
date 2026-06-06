<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceActionRequest;
use App\Models\Attendance;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $today = today();
        $selectedStaffId = old('staff_id', $request->integer('staff_id') ?: null);

        $staff = Staff::query()
            ->where('active', true)
            ->with(['attendances' => fn ($query) => $query
                ->visibleOnDate($today)
                ->orderByRaw('checked_out_at is null desc')
                ->latest('work_date')
            ])
            ->orderBy('name')
            ->get();

        $statuses = $staff->mapWithKeys(function (Staff $staffMember) {
            $attendance = $staffMember->attendances->first();

            return [
                $staffMember->id => [
                    'label' => $this->statusLabel($attendance),
                    'checked_in_at' => $attendance?->checked_in_at?->format('g:i A'),
                    'checked_out_at' => $attendance?->checked_out_at?->format('g:i A'),
                    'worked_duration' => $attendance?->workedDuration(),
                    'check_in_ip' => $attendance?->check_in_ip,
                ],
            ];
        });

        return view('attendance.index', [
            'staff' => $staff,
            'statuses' => $statuses,
            'selectedStaffId' => $selectedStaffId,
            'today' => $today,
        ]);
    }

    public function checkIn(AttendanceActionRequest $request)
    {
        $staffMember = $this->validatedStaffMember($request);
        $today = today();

        $alreadyCheckedIn = Attendance::query()
            ->where('staff_id', $staffMember->id)
            ->where(fn ($query) => $query
                ->whereDate('work_date', $today)
                ->orWhere(fn ($query) => $query->open())
            )
            ->exists();

        if ($alreadyCheckedIn) {
            return back()
                ->withInput($request->only('staff_id'))
                ->with('error', 'You have already checked in today.');
        }

        Attendance::create([
            'staff_id' => $staffMember->id,
            'work_date' => $today,
            'checked_in_at' => now(),
            'check_in_ip' => $request->ip(),
        ]);

        return back()
            ->withInput($request->only('staff_id'))
            ->with('success', 'Check-in recorded.');
    }

    public function checkOut(AttendanceActionRequest $request)
    {
        $staffMember = $this->validatedStaffMember($request);
        $today = today();

        $attendance = Attendance::query()
            ->where('staff_id', $staffMember->id)
            ->open()
            ->latest('work_date')
            ->first();

        if (! $attendance) {
            $attendance = Attendance::query()
                ->where('staff_id', $staffMember->id)
                ->whereDate('work_date', $today)
                ->first();
        }

        if (! $attendance || ! $attendance->checked_in_at) {
            return back()
                ->withInput($request->only('staff_id'))
                ->with('error', 'You need to check in before checking out.');
        }

        if ($attendance->checked_out_at) {
            return back()
                ->withInput($request->only('staff_id'))
                ->with('error', 'You have already checked out today.');
        }

        $attendance->update([
            'checked_out_at' => now(),
        ]);

        return back()
            ->withInput($request->only('staff_id'))
            ->with('success', 'Check-out recorded.');
    }

    private function validatedStaffMember(AttendanceActionRequest $request): Staff
    {
        $validated = $request->validated();

        $staffMember = Staff::query()
            ->where('active', true)
            ->findOrFail($validated['staff_id']);

        if (! Hash::check($validated['pin'], $staffMember->pin_hash)) {
            throw ValidationException::withMessages([
                'pin' => 'The staff PIN is incorrect.',
            ]);
        }

        return $staffMember;
    }

    private function statusLabel(?Attendance $attendance): string
    {
        if (! $attendance?->checked_in_at) {
            return 'Not checked in';
        }

        if (! $attendance->checked_out_at) {
            return 'Checked in';
        }

        return 'Checked out';
    }
}
