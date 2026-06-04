@extends('admin.layout')

@section('title', 'Shifts')

@section('content')
    <div class="top">
        <h2>Shifts</h2>
        <a class="button" href="{{ route('admin.shifts.create') }}">Add Shift</a>
    </div>

    <section class="panel pad">
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Starts</th>
                <th>Ends</th>
                <th>Grace</th>
                <th>Staff</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($shifts as $shift)
                <tr>
                    <td>{{ $shift->name }}</td>
                    <td>{{ $shift->starts_at->format('H:i') }}</td>
                    <td>{{ $shift->ends_at->format('H:i') }}</td>
                    <td>{{ $shift->grace_minutes }} min</td>
                    <td>{{ $shift->staff_count }}</td>
                    <td>{{ $shift->active ? 'Active' : 'Inactive' }}</td>
                    <td>
                        <div class="actions">
                            <a class="button secondary" href="{{ route('admin.shifts.edit', $shift) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.shifts.destroy', $shift) }}">
                                @csrf
                                @method('DELETE')
                                <button class="danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">No shifts found.</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $shifts->links() }}
    </section>
@endsection
