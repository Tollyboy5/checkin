@extends('admin.layout')

@section('title', 'Attendance')

@section('content')
    <div class="top">
        <h2>Attendance Records</h2>
        <form class="actions" method="GET" action="{{ route('admin.attendance.index') }}">
            <input name="date" type="date" value="{{ $date->toDateString() }}">
            <button type="submit">View Date</button>
        </form>
    </div>

    <section class="panel pad">
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Shift</th>
                <th>Date</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Hours</th>
                <th>Check-in IP</th>
                <th>Check-out IP</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->staff->name }}</td>
                    <td>{{ $attendance->staff->shift?->name ?? 'Unassigned' }}</td>
                    <td>{{ $attendance->work_date->toDateString() }}</td>
                    <td>{{ $attendance->checked_in_at?->format('g:i A') ?? '-' }}</td>
                    <td>{{ $attendance->checked_out_at?->format('g:i A') ?? '-' }}</td>
                    <td>{{ $attendance->workedDuration() ?? '-' }}</td>
                    <td>{{ $attendance->check_in_ip ?? '-' }}</td>
                    <td>{{ $attendance->check_out_ip ?? '-' }}</td>
                    <td><a class="button secondary" href="{{ route('admin.attendance.edit', $attendance) }}">Edit</a></td>
                </tr>
            @empty
                <tr><td colspan="9">No attendance records for this date.</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $attendances->links() }}
    </section>
@endsection
