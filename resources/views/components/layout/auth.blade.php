<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Login' }}</title>
    <link rel="stylesheet" href="/css/tokens.css">
</head>
<body>
<div class="auth-layout">
    {{ $slot }}
</div>
</body>
</html>
