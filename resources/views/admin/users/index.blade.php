@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 mb-1 fw-semibold">User Management</h1>
                    <p class="text-muted mb-0">Manage system users and their permissions</p>
                </div>
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-2"></i>Add New User
                </a>
            </div>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Search by name, email, phone...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="roleFilter" class="form-select">
                        <option value="">All Roles</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-secondary w-100" id="clearFilters">
                        <i class="bi bi-x-circle me-1"></i> Clear
                    </button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table id="usersTable" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Created</th>
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

@push('scripts')
<script src="{{ asset('js/users/index.js') }}"></script>
@endpush
@endsection

