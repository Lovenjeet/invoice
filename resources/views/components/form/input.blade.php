@props(['name', 'label', 'type' => 'text', 'value' => '', 'required' => false, 'placeholder' => '', 'id' => null])

@php
    $id = $id ?? $name;
    $oldValue = old($name, $value);
@endphp

<div class="mb-3">
    @if($label)
        <label for="{{ $id }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <input 
        type="{{ $type }}" 
        class="form-control @error($name) is-invalid @enderror" 
        id="{{ $id }}"
        name="{{ $name }}" 
        value="{{ $oldValue }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        {{ $attributes }}
    >
    
    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

