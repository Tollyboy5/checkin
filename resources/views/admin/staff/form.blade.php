@extends('admin.layout')

@section('title', $staffMember->exists ? 'Edit Staff' : 'Add Staff')

@section('content')
    <div class="top">
        <h2>{{ $staffMember->exists ? 'Edit Staff' : 'Add Staff' }}</h2>
        <a class="button secondary" href="{{ route('admin.staff.index') }}">Back</a>
    </div>

    <section class="panel pad">
        <form method="POST" action="{{ $staffMember->exists ? route('admin.staff.update', $staffMember) : route('admin.staff.store') }}">
            @csrf
            @if ($staffMember->exists)
                @method('PUT')
            @endif

            <div class="form-grid">
                <div class="field">
                    <label for="name">Name</label>
                    <input id="name" name="name" value="{{ old('name', $staffMember->name) }}" required>
                </div>
                <div class="field">
                    <label for="shift_id">Shift</label>
                    <select id="shift_id" name="shift_id">
                        <option value="">Unassigned</option>
                        @foreach ($shifts as $shift)
                            <option value="{{ $shift->id }}" @selected(old('shift_id', $staffMember->shift_id) == $shift->id)>
                                {{ $shift->name }} ({{ $shift->starts_at->format('H:i') }}-{{ $shift->ends_at->format('H:i') }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="pin">PIN</label>
                    <input id="pin" name="pin" type="password" inputmode="numeric" @required(! $staffMember->exists)>
                </div>
                <div class="field">
                    <label for="active">Status</label>
                    <select id="active" name="active" required>
                        <option value="1" @selected(old('active', $staffMember->active) == 1)>Active</option>
                        <option value="0" @selected(old('active', $staffMember->active) == 0)>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="actions" style="margin-top: 18px;">
                <button type="submit">Save Staff</button>
            </div>
        </form>
    </section>
@endsection
