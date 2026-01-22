Hello {{ $customer->first_name ?? $customer->email }},

Your customer portal link:
{{ $link }}

This link expires soon.
