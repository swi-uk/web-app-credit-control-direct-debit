<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Portal' }}</title>
    <link rel="stylesheet" href="/css/tokens.css">
</head>
<body>
<div class="portal-layout">
    <div class="text-h2">{{ $title ?? 'Portal' }}</div>
    <div class="portal-nav">
        <a href="{{ route('portal.dashboard') }}">Overview</a>
        <a href="{{ route('portal.payments') }}">Payments</a>
        <a href="{{ route('portal.mandates') }}">Direct Debit</a>
    </div>
    {{ $slot }}
</div>
</body>
</html>
