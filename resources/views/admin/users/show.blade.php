@extends('layouts.admin')

@section('title', $user->name)

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}" class="text-decoration-none">Users</a></li>
            <li class="breadcrumb-item active">{{ $user->name }}</li>
        </ol>
    </nav>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1 fw-bold">{{ $user->name }}</h1>
            <p class="text-muted mb-0">
                @if($user->role === 'admin')
                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-1 rounded-pill">Admin</span>
                @else
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill">User</span>
                @endif
                <span class="ms-2 text-muted">
                    <i class="bi bi-calendar3 me-1"></i>Joined {{ $user->created_at->format('M d, Y') }}
                </span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i>Edit
            </a>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- User Information -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-person me-2 text-primary"></i>User Information
                    </h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted">Name:</dt>
                        <dd class="col-sm-8 fw-medium">{{ $user->name }}</dd>
                        
                        <dt class="col-sm-4 text-muted">Email:</dt>
                        <dd class="col-sm-8">{{ $user->email }}</dd>
                        
                        <dt class="col-sm-4 text-muted">Phone:</dt>
                        <dd class="col-sm-8">{{ $user->phone ?? 'N/A' }}</dd>
                        
                        <dt class="col-sm-4 text-muted">Role:</dt>
                        <dd class="col-sm-8">
                            @if($user->role === 'admin')
                                <span class="badge bg-danger bg-opacity-10 text-danger">Admin</span>
                            @else
                                <span class="badge bg-primary bg-opacity-10 text-primary">User</span>
                            @endif
                        </dd>
                        
                        <dt class="col-sm-4 text-muted">Email Verified:</dt>
                        <dd class="col-sm-8">
                            @if($user->email_verified_at)
                                <span class="badge bg-success">Verified</span>
                                <small class="text-muted ms-2">{{ $user->email_verified_at->format('M d, Y') }}</small>
                            @else
                                <span class="badge bg-warning">Not Verified</span>
                            @endif
                        </dd>
                        
                        <dt class="col-sm-4 text-muted">Created At:</dt>
                        <dd class="col-sm-8">{{ $user->created_at->format('M d, Y h:i A') }}</dd>
                        
                        <dt class="col-sm-4 text-muted">Last Updated:</dt>
                        <dd class="col-sm-8">{{ $user->updated_at->format('M d, Y h:i A') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Account Status -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-shield-check me-2 text-success"></i>Account Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-3">
                        <div>
                            <label class="text-muted small">Account Status</label>
                            <div>
                                <span class="badge bg-success">Active</span>
                            </div>
                        </div>
                        @if($user->id === auth()->id())
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle me-2"></i>This is your account
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

