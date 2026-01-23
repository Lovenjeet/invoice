@extends('layouts.admin')

@section('title', 'View Bill To')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('bill-tos.index') }}" class="text-decoration-none">Bill To</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 fw-semibold">Bill To Details</h1>
                    <p class="text-muted mb-0">View bill to information</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('bill-tos.edit', $billTo->id) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                    <a href="{{ route('bill-tos.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="row g-4">
                @if($billTo->location)
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase fw-semibold">Location</label>
                        <p class="mb-0">{{ $billTo->location }}</p>
                    </div>
                </div>
                @endif
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase fw-semibold">Name</label>
                        <p class="mb-0 fw-medium">{{ $billTo->name }}</p>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase fw-semibold">Address Line 1</label>
                        <p class="mb-0">{{ $billTo->address1 }}</p>
                    </div>
                </div>
                @if($billTo->address2)
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase fw-semibold">Address Line 2</label>
                        <p class="mb-0">{{ $billTo->address2 }}</p>
                    </div>
                </div>
                @endif
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase fw-semibold">City</label>
                        <p class="mb-0">{{ $billTo->city ?? 'N/A' }}</p>
                    </div>
                </div>
                @if($billTo->contact2)
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase fw-semibold">Contact</label>
                        <p class="mb-0">{{ $billTo->contact2 }}</p>
                    </div>
                </div>
                @endif
                @if($billTo->vat_eori)
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase fw-semibold">VAT/EORI</label>
                        <p class="mb-0">{{ $billTo->vat_eori }}</p>
                    </div>
                </div>
                @endif
                @if($billTo->vat_eori2)
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase fw-semibold">VAT/EORI 2</label>
                        <p class="mb-0">{{ $billTo->vat_eori2 }}</p>
                    </div>
                </div>
                @endif
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase fw-semibold">Created At</label>
                        <p class="mb-0">{{ $billTo->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-muted small text-uppercase fw-semibold">Updated At</label>
                        <p class="mb-0">{{ $billTo->updated_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

