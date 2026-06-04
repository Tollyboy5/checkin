<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Roster;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->date('date') ?: today();
        $date = Carbon::parse($date)->startOfDay();

        $staff = Staff::query()
            ->where('active', true)
            ->with([
                'shift',
                'rosters.shift',
                'attendances' => fn ($query) => $query->whereDate('work_date', $date),
            ])
            ->orderBy('name')
            ->get();

        $rows = $staff->map(function (Staff $staffMember) use ($date) {
            $attendance = $staffMember->attendances->first();
            $shift = $this->shiftForDate($staffMember, $date);
            $status = $this->attendanceStatus($attendance, $shift, $date);

            return [
                'staff' => $staffMember,
                'attendance' => $attendance,
                'shift' => $shift,
                'status' => $status,
                'grade' => $this->grade($status),
            ];
        });
        $monthlyPunctuality = $this->monthlyPunctuality($staff, $date);

        return view('admin.dashboard', [
            'date' => $date,
            'rows' => $rows,
            'dailyAttendances' => Attendance::query()
                ->with('staff.shift')
                ->whereDate('work_date', $date)
                ->orderBy('checked_in_at')
                ->get(),
            'lateStaff' => $rows->where('status', 'Late')->values(),
            'absentStaff' => $rows->where('status', 'Absent')->values(),
            'gradeRows' => $rows,
            'totals' => [
                'staff' => $rows->count(),
                'present' => $rows->whereIn('status', ['Present', 'Late'])->count(),
                'late' => $rows->where('status', 'Late')->count(),
                'absent' => $rows->where('status', 'Absent')->count(),
            ],
            'bestPunctualStaff' => $monthlyPunctuality->sortByDesc('score')->first(),
            'worstPunctualStaff' => $monthlyPunctuality->sortBy('score')->first(),
        ]);
    }

    private function monthlyPunctuality($staff, Carbon $date)
    {
        $start = $date->copy()->startOfMonth();
        $end = $date->copy()->endOfMonth();

        $attendances = Attendance::query()
            ->whereBetween('work_date', [$start, $end])
            ->get()
            ->groupBy(fn (Attendance $attendance) => $attendance->staff_id.'|'.$attendance->work_date->toDateString());

        $rosters = Roster::query()
            ->with('shift')
            ->whereBetween('roster_date', [$start, $end])
            ->get()
            ->groupBy(fn (Roster $roster) => $roster->staff_id.'|'.$roster->roster_date->toDateString());

        $daysElapsed = max(1, $start->diffInDays($date) + 1);

        return $staff->map(function (Staff $staffMember) use ($attendances, $date, $daysElapsed, $rosters, $start) {
            $onTime = 0;
            $late = 0;
            $absent = 0;

            for ($day = $start->copy(); $day->lte($date); $day->addDay()) {
                $key = $staffMember->id.'|'.$day->toDateString();
                $attendance = $attendances->get($key)?->first();
                $shift = $rosters->get($key)?->first()?->shift ?? $staffMember->shift;
                $status = $this->attendanceStatus($attendance, $shift, $day);

                if ($status === 'Present') {
                    $onTime++;
                } elseif ($status === 'Late') {
                    $late++;
                } else {
                    $absent++;
                }
            }

            return [
                'staff' => $staffMember,
                'on_time' => $onTime,
                'late' => $late,
                'absent' => $absent,
                'score' => round(($onTime / $daysElapsed) * 100, 1),
            ];
        });
    }

    private function shiftForDate(Staff $staffMember, Carbon $date)
    {
        return $staffMember->rosters
            ->first(fn (Roster $roster) => $roster->roster_date->isSameDay($date))
            ?->shift ?? $staffMember->shift;
    }

    private function attendanceStatus(?Attendance $attendance, $shift, Carbon $date): string
    {
        if (! $attendance?->checked_in_at) {
            return 'Absent';
        }

        $lateCutoff = $shift
            ? Carbon::parse($date->toDateString().' '.$shift->starts_at->format('H:i:s'))->addMinutes($shift->grace_minutes)
            : null;

        if ($lateCutoff && $attendance->checked_in_at->greaterThan($lateCutoff)) {
            return 'Late';
        }

        return 'Present';
    }

    private function grade(string $status): string
    {
        return match ($status) {
            'Present' => 'A',
            'Late' => 'C',
            default => 'F',
        };
    }
}
