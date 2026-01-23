@props(['name', 'label', 'options' => [], 'value' => '', 'required' => false, 'id' => null, 'placeholder' => 'Select an option'])

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
    
    <select 
        class="form-select @error($name) is-invalid @enderror" 
        id="{{ $id }}"
        name="{{ $name }}"
        @if($required) required @endif
        {{ $attributes }}
    >
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" {{ $oldValue == $optionValue ? 'selected' : '' }}>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>
    
    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

