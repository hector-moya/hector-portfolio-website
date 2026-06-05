<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="robots" content="noindex" />
    <title>@yield('code') · {{ config('app.name') }}</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <style>
        :root {
            --bg: oklch(0.10 0.04 145);
            --bg-mid: oklch(0.14 0.055 148);
            --fg: oklch(0.94 0.025 88);
            --muted: oklch(0.62 0.05 148);
            --solar: oklch(0.78 0.19 82);
            --bio: oklch(0.72 0.17 178);
            --border: oklch(0.30 0.08 148 / 0.5);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background:
                radial-gradient(ellipse 80% 60% at 50% 0%, oklch(0.22 0.09 142 / 0.45) 0%, transparent 65%),
                var(--bg);
            color: var(--fg);
            font-family: 'Space Grotesk', ui-sans-serif, system-ui, sans-serif;
            text-align: center;
            padding: 1.5rem;
        }
        .card { max-width: 32rem; }
        .code {
            font-size: clamp(4rem, 18vw, 9rem);
            font-weight: 900;
            line-height: 1;
            color: var(--solar);
            text-shadow: 0 0 40px oklch(0.78 0.19 82 / 0.4);
            letter-spacing: -0.02em;
        }
        h1 {
            margin-top: 1rem;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--fg);
        }
        p { margin-top: 0.75rem; color: var(--muted); line-height: 1.7; }
        .home {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 2rem;
            padding: 0.85rem 2rem;
            border-radius: 0.375rem;
            background: var(--solar);
            color: var(--bg);
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            text-decoration: none;
            box-shadow: 0 0 24px oklch(0.78 0.19 82 / 0.35);
            transition: box-shadow 0.3s, transform 0.3s;
        }
        .home:hover, .home:focus-visible {
            box-shadow: 0 0 44px oklch(0.78 0.19 82 / 0.5);
            transform: translateY(-2px);
            outline: none;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="code">@yield('code')</div>
        <h1>@yield('title')</h1>
        <p>@yield('message')</p>
        <a href="{{ url('/') }}" class="home">{{ __('Back to home') }}</a>
    </div>
</body>
</html>
