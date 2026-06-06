@extends('admin.layout')

@section('title', 'Edit Attendance')

@section('content')
    <div class="top">
        <h2>Edit Attendance</h2>
        <a class="button secondary" href="{{ route('admin.attendance.index', ['date' => $attendance->work_date->toDateString()]) }}">Back</a>
    </div>

    <section class="panel pad">
        <form method="POST" action="{{ route('admin.attendance.update', $attendance) }}">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="field">
                    <label for="staff_id">Staff</label>
                    <select id="staff_id" name="staff_id" required>
                        @foreach ($staff as $staffMember)
                            <option value="{{ $staffMember->id }}" @selected(old('staff_id', $attendance->staff_id) == $staffMember->id)>
                                {{ $staffMember->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="work_date">Date</label>
                    <input id="work_date" name="work_date" type="date" value="{{ old('work_date', $attendance->work_date->toDateString()) }}" required>
                </div>
                <div class="field">
                    <label for="checked_in_at">Check In</label>
                    <input id="checked_in_at" name="checked_in_at" type="time" value="{{ old('checked_in_at', $attendance->checked_in_at?->format('H:i')) }}">
                </div>
                <div class="field">
                    <label for="checked_out_at">Check Out</label>
                    <input id="checked_out_at" name="checked_out_at" type="time" value="{{ old('checked_out_at', $attendance->checked_out_at?->format('H:i')) }}">
                </div>
                <div class="field full">
                    <label for="check_in_ip">Check-in IP</label>
                    <input id="check_in_ip" name="check_in_ip" value="{{ old('check_in_ip', $attendance->check_in_ip) }}">
                </div>
                <div class="field full">
                    <label for="check_out_ip">Check-out IP</label>
                    <input id="check_out_ip" name="check_out_ip" value="{{ old('check_out_ip', $attendance->check_out_ip) }}">
                </div>
            </div>

            <div class="actions" style="margin-top: 18px;">
                <button type="submit">Save Record</button>
            </div>
        </form>
    </section>
@endsection
