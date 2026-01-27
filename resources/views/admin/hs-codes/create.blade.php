@extends('layouts.admin')

@section('title', 'Create SKU')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('hs-codes.index') }}" class="text-decoration-none">SKUs</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1 fw-semibold">Create New SKU</h1>
            <p class="text-muted mb-0">Fill in the details below to add a new SKU</p>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('hs-codes.store') }}" id="hsCodeForm">
                @csrf
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <x-form.input name="model" label="Model" :value="old('model')" placeholder="Enter model" required />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="sku" label="SKU" :value="old('sku')" placeholder="Enter SKU" required />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="hs_code" label="HS Code" :value="old('hs_code')" placeholder="Enter HS Code" required />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="dimensions" label="Dimensions" :value="old('dimensions')" placeholder="Enter dimensions" />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="number_of_units" label="Number of Units" type="number" :value="old('number_of_units')" placeholder="Enter number of units" required />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="weight" label="Weight" type="number" step="0.01" :value="old('weight')" placeholder="Enter weight" required />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="temp_selected" label="Temp Selected" :value="old('temp_selected')" placeholder="Enter temp selected" />
                    </div>
                    <div class="col-md-6">
                        <x-form.select name="dg" label="DG" :value="old('dg')" :options="['Yes' => 'Yes', 'No' => 'No']" placeholder="Select..." />
                    </div>
                    <div class="col-md-12">
                        <x-form.input name="description" label="Description" :value="old('description')" placeholder="Enter description" />
                    </div>
                    <div class="col-md-4">
                        <x-form.input name="desc1" label="Description 1" :value="old('desc1')" placeholder="Enter description 1" />
                    </div>
                    <div class="col-md-4">
                        <x-form.input name="desc2" label="Description 2" :value="old('desc2')" placeholder="Enter description 2" />
                    </div>
                    <div class="col-md-4">
                        <x-form.input name="desc3" label="Description 3" :value="old('desc3')" placeholder="Enter description 3" />
                    </div>
                </div>
                
                <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                    <a href="{{ route('hs-codes.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>Create SKU
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/hs-codes/form.js') }}"></script>
@endpush
@endsection

