@props(['label' => null, 'type' => 'text', 'name' => null, 'value' => null])
<div class="form-field">
    @if ($label)
        <label class="form-label" for="{{ $name }}">{{ $label }}</label>
    @endif
    <input class="input" type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" value="{{ old($name, $value) }}" {{ $attributes }}>
</div>
