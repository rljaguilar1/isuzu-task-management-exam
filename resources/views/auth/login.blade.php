<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in - {{ config('app.name') }}</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f8fafc;
            color: #0f172a;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 24px;
            background:
                radial-gradient(circle at top left, rgba(16, 185, 129, 0.12), transparent 32%),
                #f8fafc;
        }

        .login-card {
            width: min(100%, 420px);
            padding: 36px;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 28px;
            font-size: 15px;
            font-weight: 700;
        }

        .brand-mark {
            width: 32px;
            height: 32px;
            display: grid;
            place-items: center;
            border-radius: 9px;
            background: #059669;
            color: #ffffff;
            font-size: 17px;
        }

        h1 {
            margin: 0 0 8px;
            font-size: 28px;
            letter-spacing: -0.025em;
        }

        .intro {
            margin: 0 0 28px;
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
        }

        .field { margin-bottom: 18px; }

        label {
            display: block;
            margin-bottom: 7px;
            font-size: 14px;
            font-weight: 600;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 11px 13px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            background: #ffffff;
            color: #0f172a;
            font: inherit;
            outline: none;
            transition: border-color 150ms, box-shadow 150ms;
        }

        input:focus {
            border-color: #059669;
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.13);
        }

        .error {
            margin: 7px 0 0;
            color: #dc2626;
            font-size: 13px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 4px 0 22px;
            color: #475569;
            font-size: 14px;
        }

        .remember input { accent-color: #059669; }

        button {
            width: 100%;
            padding: 12px 16px;
            border: 0;
            border-radius: 9px;
            background: #059669;
            color: #ffffff;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
        }

        button:hover { background: #047857; }

        .register {
            margin: 22px 0 0;
            text-align: center;
            color: #64748b;
            font-size: 14px;
        }

        a {
            color: #047857;
            font-weight: 600;
            text-decoration: none;
        }

        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <main class="login-card">
        <div class="brand">
            <span class="brand-mark">✓</span>
            <span>Task Manager</span>
        </div>

        <h1>Welcome back</h1>
        <p class="intro">Sign in once and you will be taken to the correct workspace for your account.</p>

        <form method="POST" action="{{ route('login.authenticate') }}">
            @csrf

            <div class="field">
                <label for="email">Email address</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email">
                @error('email')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required autocomplete="current-password">
                @error('password')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <label class="remember">
                <input name="remember" type="checkbox" value="1">
                <span>Remember me</span>
            </label>

            <button type="submit">Sign in</button>
        </form>

        <p class="register">New here? <a href="{{ route('filament.app.auth.register') }}">Create an account</a></p>
    </main>
</body>
</html>
