<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between gap-2 align-items-md-end mb-4">
        <div>
            <p class="text-uppercase text-secondary fw-bold mb-1">Local attendance kiosk</p>
            <h1 class="display-5 fw-bold mb-0">Staff Attendance</h1>
        </div>
        <p class="fw-semibold text-secondary mb-0">{{ $today->format('l, F j, Y') }}</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($staff->isEmpty())
        <div class="alert alert-warning">No active staff are available.</div>
    @else
        <div class="row g-4">
            <section class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h2 class="h4 fw-bold mb-3">Check In</h2>
                        <form method="POST" action="{{ route('attendance.check-in') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="check_in_staff_id">Staff</label>
                                <select class="form-select" id="check_in_staff_id" name="staff_id" required>
                                    @foreach ($staff as $staffMember)
                                        <option value="{{ $staffMember->id }}" @selected((string) $selectedStaffId === (string) $staffMember->id)>
                                            {{ $staffMember->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="check_in_pin">PIN</label>
                                <input class="form-control" id="check_in_pin" name="pin" type="password" inputmode="numeric" autocomplete="current-password" required>
                            </div>
                            <button class="btn btn-success w-100 fw-bold" type="submit">Check In</button>
                        </form>
                    </div>
                </div>
            </section>

            <section class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h2 class="h4 fw-bold mb-3">Check Out</h2>
                        <form method="POST" action="{{ route('attendance.check-out') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="check_out_staff_id">Staff</label>
                                <select class="form-select" id="check_out_staff_id" name="staff_id" required>
                                    @foreach ($staff as $staffMember)
                                        <option value="{{ $staffMember->id }}" @selected((string) $selectedStaffId === (string) $staffMember->id)>
                                            {{ $staffMember->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="check_out_pin">PIN</label>
                                <input class="form-control" id="check_out_pin" name="pin" type="password" inputmode="numeric" autocomplete="current-password" required>
                            </div>
                            <button class="btn btn-danger w-100 fw-bold" type="submit">Check Out</button>
                        </form>
                    </div>
                </div>
            </section>
        </div>

        <section class="card border-0 shadow-sm mt-4">
            <div class="card-body p-4">
                <h2 class="h4 fw-bold mb-3">Today's Status</h2>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                        <tr>
                            <th>Staff</th>
                            <th>Status</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Hours</th>
                            <th>Check-in IP</th>
                            <th>Check-out IP</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($staff as $staffMember)
                            @php($status = $statuses[$staffMember->id])
                            <tr>
                                <td class="fw-semibold">{{ $staffMember->name }}</td>
                                <td><span class="badge text-bg-secondary">{{ $status['label'] }}</span></td>
                                <td>{{ $status['checked_in_at'] ?? '-' }}</td>
                                <td>{{ $status['checked_out_at'] ?? '-' }}</td>
                                <td>{{ $status['worked_duration'] ?? '-' }}</td>
                                <td>{{ $status['check_in_ip'] ?? '-' }}</td>
                                <td>{{ $status['check_out_ip'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    @endif
</main>
</body>
</html>
