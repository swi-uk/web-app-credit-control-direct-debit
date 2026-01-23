@props(['label' => null, 'name' => null, 'rows' => 4])
<div class="form-field">
    @if ($label)
        <label class="form-label" for="{{ $name }}">{{ $label }}</label>
    @endif
    <textarea class="textarea" name="{{ $name }}" id="{{ $name }}" rows="{{ $rows }}" {{ $attributes }}>{{ old($name) }}</textarea>
</div>
