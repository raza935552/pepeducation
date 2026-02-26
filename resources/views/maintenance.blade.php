<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'PepProfesor') }} — Maintenance</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #FAF9F7;
            color: #1a1a1a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            text-align: center;
            max-width: 440px;
            padding: 2rem;
        }
        .logo-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #9A7B4F 0%, #C4A265 100%);
            border-radius: 16px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 14px rgba(154, 123, 79, 0.25);
        }
        .logo-mark svg { width: 32px; height: 32px; color: #fff; }
        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 0.5rem;
        }
        .message {
            font-size: 0.95rem;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .divider {
            width: 48px;
            height: 2px;
            background: #e5e7eb;
            margin: 0 auto 1.5rem;
        }
        .access-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.75rem;
        }
        .form-row {
            display: flex;
            gap: 0.5rem;
        }
        input[type="password"] {
            flex: 1;
            padding: 0.625rem 0.875rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.875rem;
            outline: none;
            transition: border-color 0.15s;
        }
        input[type="password"]:focus {
            border-color: #9A7B4F;
            box-shadow: 0 0 0 3px rgba(154, 123, 79, 0.1);
        }
        button {
            padding: 0.625rem 1.25rem;
            background: #9A7B4F;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
            white-space: nowrap;
        }
        button:hover { background: #836A43; }
        .error {
            margin-top: 0.5rem;
            font-size: 0.8rem;
            color: #dc2626;
        }
        .footer {
            margin-top: 3rem;
            font-size: 0.75rem;
            color: #d1d5db;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-mark">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0 1 12 15a9.065 9.065 0 0 0-6.23.693L5 14.5m14.8.8 1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0 1 12 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/>
            </svg>
        </div>

        <h1>We'll Be Right Back</h1>
        <p class="message">{{ $message }}</p>

        <div class="divider"></div>

        <p class="access-label">Team Access</p>
        <form method="POST" action="{{ url('/maintenance/unlock') }}">
            @csrf
            <div class="form-row">
                <input type="password" name="password" placeholder="Enter QA password" autocomplete="off" autofocus>
                <button type="submit">Enter</button>
            </div>
            @error('password')
                <p class="error">{{ $message }}</p>
            @enderror
        </form>

        <p class="footer">&copy; {{ date('Y') }} {{ config('app.name', 'PepProfesor') }}</p>
    </div>
</body>
</html>
