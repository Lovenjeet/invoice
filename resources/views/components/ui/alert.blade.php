@props(['type' => 'info', 'message' => '', 'dismissible' => true])

@php
    $alertClass = match($type) {
        'success' => 'alert-success',
        'danger' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info',
        default => 'alert-info',
    };
    
    $icon = match($type) {
        'success' => 'bi-check-circle',
        'danger' => 'bi-exclamation-triangle',
        'warning' => 'bi-exclamation-circle',
        'info' => 'bi-info-circle',
        default => 'bi-info-circle',
    };
@endphp

<div class="alert {{ $alertClass }} alert-dismissible fade show" role="alert">
    <i class="bi {{ $icon }} me-2"></i>
    {{ $message ?: $slot }}
    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>

