@props(['type' => 'neutral'])
@php
    $class = match ($type) {
        'success' => 'badge-success',
        'warning' => 'badge-warning',
        'danger' => 'badge-danger',
        default => 'badge-neutral',
    };
@endphp
<div class="card">
    <div class="badge {{ $class }}">{{ ucfirst($type) }}</div>
    <div class="text-body">{{ $slot }}</div>
</div>
