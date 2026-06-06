@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
    <div class="top">
        <h2>Dashboard</h2>
        <form class="actions" method="GET" action="{{ route('admin.dashboard') }}">
            <input name="date" type="date" value="{{ $date->toDateString() }}">
            <button type="submit">View Date</button>
        </form>
    </div>

    <section class="grid dashboard-widgets">
        <div class="panel pad stat"><span>Staff</span><strong>{{ $totals['staff'] }}</strong></div>
        <div class="panel pad stat"><span>Present</span><strong>{{ $totals['present'] }}</strong></div>
        <div class="panel pad stat"><span>Late</span><strong>{{ $totals['late'] }}</strong></div>
        <div class="panel pad stat"><span>Absent</span><strong>{{ $totals['absent'] }}</strong></div>
        <div class="panel pad stat">
            <span>Best This Month</span>
            <strong style="font-size: 1.15rem;">{{ $bestPunctualStaff['staff']->name ?? '-' }}</strong>
            <p class="muted mb-0">{{ $bestPunctualStaff['score'] ?? 0 }}% on time</p>
        </div>
        <div class="panel pad stat">
            <span>Worst This Month</span>
            <strong style="font-size: 1.15rem;">{{ $worstPunctualStaff['staff']->name ?? '-' }}</strong>
            <p class="muted mb-0">{{ $worstPunctualStaff['score'] ?? 0 }}% on time</p>
        </div>
    </section>

    <section class="panel pad" style="margin-top: 18px;">
        <div class="top">
            <h2>Daily Attendance</h2>
            <a class="button secondary" href="{{ route('admin.attendance.index', ['date' => $date->toDateString()]) }}">Edit Records</a>
        </div>
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Shift</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Hours</th>
                <th>Check-in IP</th>
                <th>Check-out IP</th>
                <th>Status</th>
                <th>Grade</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($gradeRows as $row)
                <tr>
                    <td>{{ $row['staff']->name }}</td>
                    <td>{{ $row['shift']?->name ?? 'Unassigned' }}</td>
                    <td>{{ $row['attendance']?->checked_in_at?->format('g:i A') ?? '-' }}</td>
                    <td>{{ $row['attendance']?->checked_out_at?->format('g:i A') ?? '-' }}</td>
                    <td>{{ $row['attendance']?->workedDuration() ?? '-' }}</td>
                    <td>{{ $row['attendance']?->check_in_ip ?? '-' }}</td>
                    <td>{{ $row['attendance']?->check_out_ip ?? '-' }}</td>
                    <td><span class="badge">{{ $row['status'] }}</span></td>
                    <td>{{ $row['grade'] }}</td>
                </tr>
            @empty
                <tr><td colspan="9">No staff found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>

    <section class="grid" style="grid-template-columns: repeat(2, minmax(0, 1fr)); margin-top: 18px;">
        <div class="panel pad">
            <h2>Late Staff</h2>
            <table>
                <tbody>
                @forelse ($lateStaff as $row)
                    <tr>
                        <td>{{ $row['staff']->name }}</td>
                        <td>{{ $row['attendance']->checked_in_at->format('g:i A') }}</td>
                    </tr>
                @empty
                    <tr><td>No late staff for this date.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="panel pad">
            <h2>Absent Staff</h2>
            <table>
                <tbody>
                @forelse ($absentStaff as $row)
                    <tr>
                        <td>{{ $row['staff']->name }}</td>
                        <td>{{ $row['shift']?->name ?? 'Unassigned shift' }}</td>
                    </tr>
                @empty
                    <tr><td>No absent staff for this date.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
