@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}" class="text-decoration-none">Users</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                    <i class="bi bi-person-gear text-primary" style="font-size: 1.5rem;"></i>
                </div>
                <div>
                    <h1 class="h3 mb-1 fw-semibold">Edit User</h1>
                    <p class="text-muted mb-0">Update the user details below</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('users.update', $user->id) }}" id="userForm">
                @csrf
                @method('PUT')
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <x-form.input name="name" label="Full Name" :value="old('name', $user->name)" placeholder="Enter full name" required />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="email" label="Email Address" type="email" :value="old('email', $user->email)" placeholder="Enter email address" required />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="phone" label="Phone Number" type="tel" :value="old('phone', $user->phone)" placeholder="Enter phone number (optional)" />
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="role" class="form-label">
                                Role <span class="text-danger">*</span>
                            </label>
                            <select 
                                class="form-select @error('role') is-invalid @enderror" 
                                id="role"
                                name="role"
                                required
                                @if($user->role === 'admin' && $user->id === auth()->id()) disabled @endif
                            >
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User</option>
                            </select>
                            @if($user->role === 'admin' && $user->id === auth()->id())
                                <input type="hidden" name="role" value="admin">
                                <small class="text-muted d-block mt-1">You cannot change your own role.</small>
                            @else
                                <small class="text-muted d-block mt-1">Select the user's role in the system</small>
                            @endif
                            @error('role')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="password" label="Password" type="password" placeholder="Leave blank to keep current password" />
                        <small class="text-muted">Leave blank if you don't want to change the password</small>
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="password_confirmation" label="Confirm Password" type="password" placeholder="Re-enter password" />
                    </div>
                </div>
                
                <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>Update User
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

