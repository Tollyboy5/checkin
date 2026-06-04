@extends('admin.layout')

@section('title', 'Rosters')

@section('content')
    <div class="top">
        <h2>Rosters</h2>
        <div class="actions">
            <form class="actions" method="GET" action="{{ route('admin.rosters.index') }}">
                <input name="date" type="date" value="{{ $date->toDateString() }}">
                <button type="submit">View Date</button>
            </form>
            <a class="button" href="{{ route('admin.rosters.create') }}">Add Roster</a>
        </div>
    </div>

    <section class="panel pad">
        <table>
            <thead>
            <tr>
                <th>Date</th>
                <th>Staff</th>
                <th>Shift</th>
                <th>Notes</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($rosters as $roster)
                <tr>
                    <td>{{ $roster->roster_date->toDateString() }}</td>
                    <td>{{ $roster->staff->name }}</td>
                    <td>{{ $roster->shift->name }}</td>
                    <td>{{ $roster->notes ?? '-' }}</td>
                    <td>
                        <div class="actions">
                            <a class="button secondary" href="{{ route('admin.rosters.edit', $roster) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.rosters.destroy', $roster) }}">
                                @csrf
                                @method('DELETE')
                                <button class="danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">No roster assignments for this date.</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $rosters->links() }}
    </section>
@endsection
