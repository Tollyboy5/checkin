@extends('admin.layout')

@section('title', $roster->exists ? 'Edit Roster' : 'Add Roster')

@section('content')
    <div class="top">
        <h2>{{ $roster->exists ? 'Edit Roster' : 'Add Roster' }}</h2>
        <a class="button secondary" href="{{ route('admin.rosters.index', ['date' => old('roster_date', $roster->roster_date?->toDateString() ?? today()->toDateString())]) }}">Back</a>
    </div>

    <section class="panel pad">
        <form method="POST" action="{{ $roster->exists ? route('admin.rosters.update', $roster) : route('admin.rosters.store') }}">
            @csrf
            @if ($roster->exists)
                @method('PUT')
            @endif

            <div class="form-grid">
                <div class="field">
                    <label for="roster_date">Date</label>
                    <input id="roster_date" name="roster_date" type="date" value="{{ old('roster_date', $roster->roster_date?->toDateString() ?? today()->toDateString()) }}" required>
                </div>
                <div class="field">
                    <label for="staff_id">Staff</label>
                    <select id="staff_id" name="staff_id" required>
                        @foreach ($staff as $staffMember)
                            <option value="{{ $staffMember->id }}" @selected(old('staff_id', $roster->staff_id) == $staffMember->id)>
                                {{ $staffMember->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="shift_id">Shift</label>
                    <select id="shift_id" name="shift_id" required>
                        @foreach ($shifts as $shift)
                            <option value="{{ $shift->id }}" @selected(old('shift_id', $roster->shift_id) == $shift->id)>
                                {{ $shift->name }} ({{ $shift->starts_at->format('H:i') }}-{{ $shift->ends_at->format('H:i') }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="notes">Notes</label>
                    <input id="notes" name="notes" value="{{ old('notes', $roster->notes) }}">
                </div>
            </div>

            <div class="actions" style="margin-top: 18px;">
                <button type="submit">Save Roster</button>
            </div>
        </form>
    </section>
@endsection
