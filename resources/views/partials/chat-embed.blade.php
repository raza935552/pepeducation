{{-- Live chat for standalone landers. Their minimal <head> has no Alpine or
     app.css, so load Alpine from the CDN (the widget is fully self-contained
     in CSS and reads its CSRF token inline). Safe here because landers never
     bundle Alpine themselves — no double-load. --}}
@once
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
@endonce
<x-chat-widget />
