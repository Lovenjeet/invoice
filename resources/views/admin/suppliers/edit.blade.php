@extends('layouts.admin')

@section('title', 'Edit Supplier')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}" class="text-decoration-none">Suppliers</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1 fw-semibold">Edit Supplier</h1>   
            <p class="text-muted mb-0">Update the supplier details below</p>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('suppliers.update', $supplier->id) }}" id="supplierForm">
                @csrf
                @method('PUT')
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <x-form.input name="name" label="Name" :value="old('name', $supplier->name)" placeholder="Enter supplier name" required />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="city" label="City" :value="old('city', $supplier->city)" placeholder="Enter city" required />
                    </div>
                    <div class="col-md-12">
                        <x-form.input name="address1" label="Address Line 1" :value="old('address1', $supplier->address1)" placeholder="Enter address" required />
                    </div>
                    <div class="col-md-12">
                        <x-form.input name="address2" label="Address Line 2" :value="old('address2', $supplier->address2)" placeholder="Enter additional address (optional)" />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="contact1" label="Contact Number 1" :value="old('contact1', $supplier->contact1)" placeholder="Enter primary contact" required />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="contact2" label="Contact Number 2" :value="old('contact2', $supplier->contact2)" placeholder="Enter secondary contact (optional)" />
                    </div>
                </div>
                
                <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>Update Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/suppliers/form.js') }}"></script>
@endpush
@endsection

