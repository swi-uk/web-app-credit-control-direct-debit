<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Admin' }}</title>
    <link rel="stylesheet" href="/css/tokens.css">
</head>
<body>
<div class="app-layout">
    <aside class="sidebar">
        <div class="brand">CCDD</div>
        <nav>
            <a href="{{ route('admin.portfolio.index') }}">Dashboard</a>
            <a href="{{ route('admin.customers.index') }}">Customers</a>
            <a href="{{ route('admin.payments.index') }}">Payments</a>
            <a href="{{ route('admin.bacs.upload') }}">Reports</a>
            <a href="{{ route('admin.refunds.index') }}">Refunds</a>
            <a href="{{ route('admin.sites.index') }}">Integrations</a>
            <a href="{{ route('admin.billing.index') }}">Billing</a>
            <a href="{{ route('admin.credit_tiers.index') }}">Settings</a>
        </nav>
    </aside>
    <div class="main">
        <div class="topbar">
            <div class="text-h2">{{ $title ?? 'Admin' }}</div>
            <div class="text-small">Search: customer/email/order</div>
        </div>
        <div class="content">
            {{ $slot }}
        </div>
    </div>
</div>
</body>
</html>
