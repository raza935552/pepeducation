<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found — {{ config('app.name', 'PepProfesor') }}</title>
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
        .container { text-align: center; max-width: 480px; padding: 2rem; }
        .code {
            font-size: 5rem;
            font-weight: 800;
            color: #9A7B4F;
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 0.75rem;
        }
        .message {
            font-size: 0.95rem;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .btn {
            display: inline-block;
            padding: 0.625rem 1.5rem;
            background: #9A7B4F;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.15s;
        }
        .btn:hover { background: #836A43; }
        .footer {
            margin-top: 3rem;
            font-size: 0.75rem;
            color: #d1d5db;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="code">404</div>
        <h1>Page Not Found</h1>
        <p class="message">The page you're looking for doesn't exist or has been moved.</p>
        <a href="{{ url('/') }}" class="btn">Back to Home</a>
        <p class="footer">&copy; {{ date('Y') }} {{ config('app.name', 'PepProfesor') }}</p>
    </div>
</body>
</html>
