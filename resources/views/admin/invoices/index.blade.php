@extends('layouts.admin')

@section('title', 'Invoices')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 mb-1 fw-semibold">Invoices</h1>
                    <p class="text-muted mb-0">Manage invoices and their details</p>
                </div>
                <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-2"></i>Create New Invoice
                </a>
            </div>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Search invoices...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select id="statusFilter" class="form-select">
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="approved">Approved</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="supplierFilter" class="form-select">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="billToFilter" class="form-select">
                        <option value="">All Bill To</option>
                        @foreach($billTos as $billTo)
                            <option value="{{ $billTo->id }}">{{ $billTo->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="shipToFilter" class="form-select">
                        <option value="">All Ship To</option>
                        @foreach($shipTos as $shipTo)
                            <option value="{{ $shipTo->id }}">{{ $shipTo->name }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- <div class="col-md-1">
                    <button type="button" class="btn btn-outline-secondary" id="clearFilters">
                        <i class="bi bi-x-circle me-1"></i>
                    </button>
                </div> --}}
            </div>
            
            <div class="table-responsive">
                <table id="invoicesTable" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>UNC Number</th>
                            <th>Email</th>
                            <th>Supplier</th>
                            <th>Bill To</th>
                            <th>Ship To</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .text-truncate-cell {
        max-width: 100px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        cursor: help;
        display: block;
    }
    
    #invoicesTable td:nth-child(3),
    #invoicesTable td:nth-child(4),
    #invoicesTable td:nth-child(5) {
        max-width: 120px;
        overflow: hidden;
    }
    
    #invoicesTable td {
        white-space: nowrap;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/invoices/index.js') }}"></script>
@endpush
@endsection

