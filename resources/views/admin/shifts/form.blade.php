@extends('admin.layout')

@section('title', $shift->exists ? 'Edit Shift' : 'Add Shift')

@section('content')
    <div class="top">
        <h2>{{ $shift->exists ? 'Edit Shift' : 'Add Shift' }}</h2>
        <a class="button secondary" href="{{ route('admin.shifts.index') }}">Back</a>
    </div>

    <section class="panel pad">
        <form method="POST" action="{{ $shift->exists ? route('admin.shifts.update', $shift) : route('admin.shifts.store') }}">
            @csrf
            @if ($shift->exists)
                @method('PUT')
            @endif

            <div class="form-grid">
                <div class="field">
                    <label for="name">Name</label>
                    <input id="name" name="name" value="{{ old('name', $shift->name) }}" required>
                </div>
                <div class="field">
                    <label for="grace_minutes">Grace Minutes</label>
                    <input id="grace_minutes" name="grace_minutes" type="number" min="0" max="240" value="{{ old('grace_minutes', $shift->grace_minutes) }}" required>
                </div>
                <div class="field">
                    <label for="starts_at">Start Time</label>
                    <input id="starts_at" name="starts_at" type="time" value="{{ old('starts_at', $shift->starts_at?->format('H:i')) }}" required>
                </div>
                <div class="field">
                    <label for="ends_at">End Time</label>
                    <input id="ends_at" name="ends_at" type="time" value="{{ old('ends_at', $shift->ends_at?->format('H:i')) }}" required>
                </div>
                <div class="field">
                    <label for="active">Status</label>
                    <select id="active" name="active" required>
                        <option value="1" @selected(old('active', $shift->active) == 1)>Active</option>
                        <option value="0" @selected(old('active', $shift->active) == 0)>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="actions" style="margin-top: 18px;">
                <button type="submit">Save Shift</button>
            </div>
        </form>
    </section>
@endsection
