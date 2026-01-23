@extends('layouts.admin')

@section('title', 'View Ship To')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('ship-tos.index') }}" class="text-decoration-none">Ship To</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 fw-semibold">Ship To Details</h1>
                    <p class="text-muted mb-0">View ship to information</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('ship-tos.edit', $shipTo->id) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                    <a href="{{ route('ship-tos.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase fw-semibold">Name</label>
                        <p class="mb-0 fw-medium">{{ $shipTo->name }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase fw-semibold">Address</label>
                        <p class="mb-0">{{ $shipTo->address ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase fw-semibold">Created At</label>
                        <p class="mb-0">{{ $shipTo->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase fw-semibold">Updated At</label>
                        <p class="mb-0">{{ $shipTo->updated_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

