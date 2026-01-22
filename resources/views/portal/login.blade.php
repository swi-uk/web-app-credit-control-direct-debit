<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Customer Portal Login</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; }
        label { display: block; margin-top: 12px; font-weight: bold; }
        input[type="email"] { width: 100%; padding: 8px; }
    </style>
</head>
<body>
    <h1>Customer Portal</h1>

    @if (!empty($sent))
        <p>If your email exists in our system, a login link has been sent.</p>
    @endif

    @if ($errors->any())
        <div style="color:#b91c1c;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('portal.login.send') }}">
        @csrf
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required>
        <div style="margin-top: 16px;">
            <button type="submit">Send login link</button>
        </div>
    </form>
</body>
</html>
