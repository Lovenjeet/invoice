@props(['type' => 'button', 'variant' => 'primary', 'size' => '', 'icon' => null, 'loading' => false])

@php
    $sizeClass = $size ? "btn-{$size}" : '';
    $classes = "btn btn-{$variant} {$sizeClass}";
    if($loading) {
        $classes .= ' disabled';
    }
@endphp

<button type="{{ $type }}" class="{{ trim($classes) }}" {{ $attributes }}>
    @if($loading)
        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
    @endif
    @if($icon && !$loading)
        <i class="{{ $icon }} me-2"></i>
    @endif
    {{ $slot }}
</button>

