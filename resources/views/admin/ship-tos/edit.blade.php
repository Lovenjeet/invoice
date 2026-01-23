@extends('layouts.admin')

@section('title', 'Edit Ship To')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('ship-tos.index') }}" class="text-decoration-none">Ship To</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1 fw-semibold">Edit Ship To</h1>
            <p class="text-muted mb-0">Update the ship to details below</p>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('ship-tos.update', $shipTo->id) }}" id="shipToForm">
                @csrf
                @method('PUT')
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <x-form.input name="name" label="Name" :value="old('name', $shipTo->name)" placeholder="Enter name" required />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="address" label="Address" :value="old('address', $shipTo->address)" placeholder="Enter address" required />
                    </div>
                </div>
                
                <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                    <a href="{{ route('ship-tos.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>Update Ship To
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/ship-tos/form.js') }}"></script>
@endpush
@endsection

