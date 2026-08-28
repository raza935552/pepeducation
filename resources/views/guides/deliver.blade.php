@php $noindex = true; @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <title>Your Research Guides</title>
    <style>
        :root { --ink:#171717; --muted:#6b7280; --accent:#C41E1E; --line:#e5e7eb; --bg:#f7f7f5; }
        * { box-sizing: border-box; }
        body { margin:0; background:var(--bg); color:var(--ink); font-family:Figtree,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif; line-height:1.5; }
        .wrap { max-width:640px; margin:0 auto; padding:40px 20px 64px; }
        .brand { font-family:'IBM Plex Mono',monospace; font-size:12px; letter-spacing:.14em; text-transform:uppercase; color:var(--muted); }
        h1 { font-size:30px; font-weight:800; margin:8px 0 6px; letter-spacing:-.01em; }
        .sub { color:var(--muted); margin:0 0 28px; }
        .card { display:flex; align-items:center; gap:16px; background:#fff; border:1px solid var(--line); border-radius:16px; padding:18px 20px; margin-bottom:12px; }
        .ic { flex:0 0 auto; width:44px; height:44px; border-radius:10px; background:#fdeaea; color:var(--accent); display:flex; align-items:center; justify-content:center; }
        .meta { flex:1 1 auto; min-width:0; }
        .name { font-weight:700; }
        .size { font-size:12px; color:var(--muted); }
        .btn { flex:0 0 auto; display:inline-flex; align-items:center; gap:6px; background:var(--ink); color:#fff; text-decoration:none; font-weight:700; font-size:14px; padding:10px 16px; border-radius:999px; }
        .empty { background:#fff; border:1px solid var(--line); border-radius:16px; padding:32px; text-align:center; color:var(--muted); }
        .fine { margin-top:28px; font-size:12px; color:var(--muted); }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="brand">Professor Peptides · Research Hub</div>
        <h1>Your research guides</h1>
        <p class="sub">Educational companion guides for what you ordered{{ $order ? ' (order #' . e($order) . ')' : '' }}. Research use only. Links are private to you and expire in 30 days.</p>

        @forelse($guides as $g)
            <div class="card">
                <span class="ic">
                    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.6a3.4 3.4 0 00-3.4-3.4h-1.5A1.1 1.1 0 0113.5 7.1V5.6a3.4 3.4 0 00-3.4-3.4H8.25M14 2.2H5.6c-.6 0-1.1.5-1.1 1.1v17.3c0 .6.5 1.1 1.1 1.1h12.8c.6 0 1.1-.5 1.1-1.1V11.2a9 9 0 00-9-9z"/></svg>
                </span>
                <div class="meta">
                    <div class="name">{{ $g['name'] }}@if($g['size']) <span class="size">· {{ $g['size'] }}</span>@endif</div>
                    <div class="size">PDF guide</div>
                </div>
                <a class="btn" href="{{ $g['url'] }}" target="_blank" rel="noopener">Download</a>
            </div>
        @empty
            <div class="empty">No companion guides are available for this order.</div>
        @endforelse

        <p class="fine">These guides are educational and for research use only, not medical advice. Professor Peptides does not sell peptides.</p>
    </div>
</body>
</html>
