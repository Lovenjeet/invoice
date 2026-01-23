@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-1 fw-semibold">Dashboard</h1>
            <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name }}!</p>
        </div>
    </div>
    
    {{-- <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-2 fw-medium">Total Products</p>
                            <h2 class="mb-0 fw-bold">1,234</h2>
                            <p class="text-success small mb-0 mt-2">
                                <i class="bi bi-arrow-up"></i> 12% from last month
                            </p>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-box-seam text-primary" style="font-size: 1.75rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-2 fw-medium">Active Products</p>
                            <h2 class="mb-0 fw-bold">987</h2>
                            <p class="text-success small mb-0 mt-2">
                                <i class="bi bi-arrow-up"></i> 5% from last month
                            </p>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-check-circle text-success" style="font-size: 1.75rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-2 fw-medium">Total Revenue</p>
                            <h2 class="mb-0 fw-bold">₹45,678</h2>
                            <p class="text-success small mb-0 mt-2">
                                <i class="bi bi-arrow-up"></i> 8% from last month
                            </p>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-currency-rupee text-info" style="font-size: 1.75rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-2 fw-medium">Total Orders</p>
                            <h2 class="mb-0 fw-bold">567</h2>
                            <p class="text-danger small mb-0 mt-2">
                                <i class="bi bi-arrow-down"></i> 3% from last month
                            </p>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-cart-check text-warning" style="font-size: 1.75rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
</div>

@push('scripts')
<script src="{{ asset('js/dashboard/dashboard.js') }}"></script>
@endpush
@endsection
