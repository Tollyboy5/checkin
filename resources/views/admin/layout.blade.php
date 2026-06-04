<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - Staff Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --ink: #17211d;
            --muted: #68756f;
            --line: #dbe3df;
            --page: #f6f8f6;
            --panel: #fff;
            --primary: #0d7c66;
            --danger: #b54231;
            --soft: #e8f4ef;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            color: var(--ink);
            background: var(--page);
        }

        a { color: inherit; text-decoration: none; }
        .shell { display: grid; grid-template-columns: 240px minmax(0, 1fr); min-height: 100vh; }
        aside { padding: 24px; background: #13221d; color: #fff; }
        aside h1 { margin: 0 0 24px; font-size: 1.35rem; }
        nav { display: grid; gap: 8px; }
        nav a, .logout {
            display: block;
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 0;
            color: #fff;
            background: transparent;
            font: inherit;
            text-align: left;
            cursor: pointer;
        }
        nav a:hover, .logout:hover { background: rgba(255, 255, 255, .12); }
        main { padding: 28px; }
        .top { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 20px; }
        h2 { margin: 0; font-size: clamp(1.6rem, 3vw, 2.4rem); }
        .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: 0 12px 26px rgba(23, 33, 29, .07);
        }
        .pad { padding: 18px; }
        .grid { display: grid; gap: 16px; }
        .stats { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .dashboard-widgets { grid-template-columns: repeat(6, minmax(0, 1fr)); }
        .stat strong { display: block; margin-top: 8px; font-size: 2rem; }
        .stat span, label { color: var(--muted); font-weight: 700; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border-bottom: 1px solid var(--line); text-align: left; vertical-align: top; }
        th { color: var(--muted); font-size: .82rem; text-transform: uppercase; }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
        .button, button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 0 12px;
            border: 0;
            border-radius: 8px;
            background: var(--primary);
            color: #fff;
            font-weight: 800;
            cursor: pointer;
        }
        .button.secondary { background: #51645d; }
        .button.danger, button.danger { background: var(--danger); }
        input, select {
            width: 100%;
            min-height: 42px;
            padding: 0 12px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fff;
        }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .field { display: grid; gap: 8px; }
        .full { grid-column: 1 / -1; }
        .message, .errors {
            margin-bottom: 16px;
            padding: 12px 14px;
            border-radius: 8px;
            font-weight: 700;
        }
        .message { color: #075a4a; background: #daf2e9; border: 1px solid #aadfcd; }
        .errors { color: #7a271a; background: #ffe3dc; border: 1px solid #f3b6aa; }
        .badge { display: inline-flex; padding: 4px 8px; border-radius: 999px; background: var(--soft); color: var(--primary); font-weight: 800; }
        .muted { color: var(--muted); }
        @media (max-width: 820px) {
            .shell { grid-template-columns: 1fr; }
            aside { position: static; }
            .stats, .dashboard-widgets, .form-grid { grid-template-columns: 1fr; }
            main { padding: 18px; }
        }
    </style>
</head>
<body>
<div class="shell">
    <aside>
        <h1>Admin</h1>
        <nav>
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('admin.staff.index') }}">Staff</a>
            <a href="{{ route('admin.shifts.index') }}">Shifts</a>
            <a href="{{ route('admin.rosters.index') }}">Rosters</a>
            <a href="{{ route('admin.attendance.index') }}">Attendance</a>
            <a href="#" aria-disabled="true">Export reports later</a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="logout" type="submit">Logout</button>
            </form>
        </nav>
    </aside>
    <main>
        @if (session('success'))
            <div class="message">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <ul class="errors">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif
        @yield('content')
    </main>
</div>
</body>
</html>
