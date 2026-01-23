@props(['status' => 'neutral'])
@php
    $map = [
        'active' => 'success',
        'restricted' => 'warning',
        'locked' => 'danger',
        'blocked' => 'danger',
        'scheduled' => 'neutral',
        'submitted' => 'warning',
        'processing' => 'warning',
        'collected' => 'success',
        'unpaid_returned' => 'danger',
        'retry_scheduled' => 'warning',
        'failed_final' => 'danger',
        'cancelled' => 'neutral',
    ];
    $class = $map[$status] ?? 'neutral';
@endphp
<span class="badge badge-{{ $class }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
