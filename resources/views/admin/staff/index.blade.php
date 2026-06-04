@extends('admin.layout')

@section('title', 'Staff')

@section('content')
    <div class="top">
        <h2>Staff</h2>
        <a class="button" href="{{ route('admin.staff.create') }}">Add Staff</a>
    </div>

    <section class="panel pad">
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Shift</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($staff as $staffMember)
                <tr>
                    <td>{{ $staffMember->name }}</td>
                    <td>{{ $staffMember->shift?->name ?? 'Unassigned' }}</td>
                    <td>{{ $staffMember->active ? 'Active' : 'Inactive' }}</td>
                    <td>
                        <div class="actions">
                            <a class="button secondary" href="{{ route('admin.staff.edit', $staffMember) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.staff.destroy', $staffMember) }}">
                                @csrf
                                @method('DELETE')
                                <button class="danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">No staff found.</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $staff->links() }}
    </section>
@endsection
