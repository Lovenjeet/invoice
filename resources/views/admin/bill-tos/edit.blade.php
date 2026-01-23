@extends('layouts.admin')

@section('title', 'Edit Bill To')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('bill-tos.index') }}" class="text-decoration-none">Bill To</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1 fw-semibold">Edit Bill To</h1>
            <p class="text-muted mb-0">Update the bill to details below</p>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('bill-tos.update', $billTo->id) }}" id="billToForm">
                @csrf
                @method('PUT')
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <x-form.input name="location" label="Location" :value="old('location', $billTo->location)" placeholder="Enter location (optional)" />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="name" label="Name" :value="old('name', $billTo->name)" placeholder="Enter name" required />
                    </div>
                    <div class="col-md-12">
                        <x-form.input name="address1" label="Address Line 1" :value="old('address1', $billTo->address1)" placeholder="Enter address" required />
                    </div>
                    <div class="col-md-12">
                        <x-form.input name="address2" label="Address Line 2" :value="old('address2', $billTo->address2)" placeholder="Enter additional address (optional)" />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="city" label="City" :value="old('city', $billTo->city)" placeholder="Enter city" required />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="contact2" label="Contact" :value="old('contact2', $billTo->contact2)" placeholder="Enter contact (optional)" />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="vat_eori" label="VAT/EORI" :value="old('vat_eori', $billTo->vat_eori)" placeholder="Enter VAT/EORI (optional)" />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="vat_eori2" label="VAT/EORI 2" :value="old('vat_eori2', $billTo->vat_eori2)" placeholder="Enter VAT/EORI 2 (optional)" />
                    </div>
                </div>
                
                <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                    <a href="{{ route('bill-tos.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>Update Bill To
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/bill-tos/form.js') }}"></script>
@endpush
@endsection

