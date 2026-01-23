@extends('layouts.admin')

@section('title', 'View Shipper')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('shippers.index') }}" class="text-decoration-none">Shippers</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 fw-semibold">Shipper Details</h1>
                    <p class="text-muted mb-0">View shipper information</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('shippers.edit', $shipper->id) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                    <a href="{{ route('shippers.index') }}" class="btn btn-outline-secondary">
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
                        <p class="mb-0 fw-medium">{{ $shipper->name }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase fw-semibold">City</label>
                        <p class="mb-0">{{ $shipper->city ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase fw-semibold">Address Line 1</label>
                        <p class="mb-0">{{ $shipper->address1 }}</p>
                    </div>
                </div>
                @if($shipper->address2)
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase fw-semibold">Address Line 2</label>
                        <p class="mb-0">{{ $shipper->address2 }}</p>
                    </div>
                </div>
                @endif
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase fw-semibold">Contact Number 1</label>
                        <p class="mb-0">{{ $shipper->contact1 ?? 'N/A' }}</p>
                    </div>
                </div>
                @if($shipper->contact2)
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase fw-semibold">Contact Number 2</label>
                        <p class="mb-0">{{ $shipper->contact2 }}</p>
                    </div>
                </div>
                @endif
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase fw-semibold">Created At</label>
                        <p class="mb-0">{{ $shipper->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase fw-semibold">Updated At</label>
                        <p class="mb-0">{{ $shipper->updated_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

