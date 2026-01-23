@props(['merchant' => null, 'title' => null])
@php
    $branding = $merchant?->branding_json ?? [];
    $logo = $branding['logo_url'] ?? null;
    $support = $branding['support_email'] ?? null;
@endphp
<table width="100%" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif; background:#f8fafc; padding:24px;">
    <tr>
        <td>
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin:0 auto; background:#ffffff; border:1px solid #e2e8f0; border-radius:12px;">
                <tr>
                    <td style="padding:24px; border-bottom:1px solid #e2e8f0;">
                        <strong>{{ $merchant?->name ?? 'CCDD' }}</strong>
                        @if ($logo)
                            <div><img src="{{ $logo }}" alt="Logo" width="48"></div>
                        @endif
                        @if ($title)
                            <div style="margin-top:8px; font-size:18px;">{{ $title }}</div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding:24px;">
                        {{ $slot }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:16px; font-size:12px; color:#64748b;">
                        {{ $support ?? config('copy.support_line') }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
