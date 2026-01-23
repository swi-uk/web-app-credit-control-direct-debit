@props(['label' => null, 'name' => null])
<div class="form-field">
    @if ($label)
        <label class="form-label" for="{{ $name }}">{{ $label }}</label>
    @endif
    <select class="select" name="{{ $name }}" id="{{ $name }}" {{ $attributes }}>
        {{ $slot }}
    </select>
</div>
