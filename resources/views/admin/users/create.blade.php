@extends('layouts.admin')

@section('title', 'Create User')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}" class="text-decoration-none">Users</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1 fw-semibold">Create New User</h1>
            <p class="text-muted mb-0">Fill in the details below to add a new user</p>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('users.store') }}" id="userForm">
                @csrf
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <x-form.input name="name" label="Name" :value="old('name')" placeholder="Enter full name" required />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="email" label="Email" type="email" :value="old('email')" placeholder="Enter email address" required />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="phone" label="Phone" type="tel" :value="old('phone')" placeholder="Enter phone number" />
                    </div>
                    <div class="col-md-6">
                        <x-form.select name="role" label="Role" :options="['admin' => 'Admin']" :value="'admin'" readonly disabled />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="password" label="Password" type="password" placeholder="Minimum 8 characters" required />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="password_confirmation" label="Confirm Password" type="password" placeholder="Re-enter password" required />
                    </div>
                </div>
                
                <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/users/form.js') }}"></script>
@endpush
@endsection

