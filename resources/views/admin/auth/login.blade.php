<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - Staff Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            font-family: Arial, Helvetica, sans-serif;
            color: #17211d;
            background: linear-gradient(135deg, #f5f7f4 0%, #eef5f7 55%, #f7f3ec 100%);
        }
        .panel {
            width: min(420px, 100%);
            padding: 24px;
            border: 1px solid #d8e2dd;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 18px 40px rgba(23, 33, 29, .09);
        }
        h1 { margin: 0 0 18px; font-size: 2rem; }
        label { display: block; margin-bottom: 8px; color: #65736d; font-weight: 800; }
        input {
            width: 100%;
            height: 46px;
            padding: 0 12px;
            border: 1px solid #d8e2dd;
            border-radius: 8px;
        }
        .field { margin-bottom: 16px; }
        .remember { display: flex; gap: 8px; align-items: center; margin-bottom: 16px; }
        .remember input { width: auto; height: auto; }
        button {
            width: 100%;
            min-height: 46px;
            border: 0;
            border-radius: 8px;
            background: #0d7c66;
            color: #fff;
            font-weight: 900;
            cursor: pointer;
        }
        .errors {
            margin: 0 0 16px;
            padding: 12px 14px;
            border-radius: 8px;
            color: #7a271a;
            background: #ffe3dc;
            border: 1px solid #f3b6aa;
            font-weight: 700;
        }
    </style>
</head>
<body>
<section class="panel">
    <h1>Admin Login</h1>
    @if ($errors->any())
        <ul class="errors">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
    <form method="POST" action="{{ route('admin.login.store') }}">
        @csrf
        <div class="field">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
        </div>
        <label class="remember">
            <input name="remember" type="checkbox" value="1">
            Remember me
        </label>
        <button type="submit">Login</button>
    </form>
</section>
</body>
</html>
