<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AttendanceRecordRequest;
use App\Models\Attendance;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceRecordController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->date('date') ?: today();

        return view('admin.attendance.index', [
            'date' => Carbon::parse($date)->startOfDay(),
            'attendances' => Attendance::query()
                ->with('staff.shift')
                ->visibleOnDate($date)
                ->orderBy('checked_in_at')
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function edit(Attendance $attendance)
    {
        return view('admin.attendance.form', [
            'attendance' => $attendance->load('staff'),
            'staff' => Staff::query()->orderBy('name')->get(),
        ]);
    }

    public function update(AttendanceRecordRequest $request, Attendance $attendance)
    {
        $validated = $request->validated();

        $workDate = Carbon::parse($validated['work_date'])->startOfDay();

        $attendance->update([
            'staff_id' => $validated['staff_id'],
            'work_date' => $workDate,
            'checked_in_at' => $this->combineDateAndTime($workDate, $validated['checked_in_at'] ?? null),
            'checked_out_at' => $this->combineDateAndTime($workDate, $validated['checked_out_at'] ?? null),
            'check_in_ip' => $validated['check_in_ip'],
        ]);

        return redirect()
            ->route('admin.attendance.index', ['date' => $workDate->toDateString()])
            ->with('success', 'Attendance record updated.');
    }

    private function combineDateAndTime(Carbon $date, ?string $time): ?Carbon
    {
        if (! $time) {
            return null;
        }

        return Carbon::parse($date->toDateString().' '.$time);
    }
}
