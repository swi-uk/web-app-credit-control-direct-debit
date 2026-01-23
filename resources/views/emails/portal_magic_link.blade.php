<x-emails.layout :merchant="$customer->merchant" title="Sign in to your portal">
    <p>Hello {{ $customer->first_name ?? $customer->email }},</p>
    <p>Your customer portal link:</p>
    <p><a href="{{ $link }}">{{ $link }}</a></p>
    <p class="text-small">This link expires soon.</p>
</x-emails.layout>
