<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login - {{ config('app.name', 'Laravel') }}</title>
    
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="card shadow-lg">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-shield-check text-primary" style="font-size: 3rem;"></i>
                        <h2 class="mt-3 mb-1">Admin Login</h2>
                        <p class="text-muted">Sign in to access the admin panel</p>
                    </div>
                    
                    @if($errors->any())
                        <x-ui.alert type="danger" :message="'Invalid credentials. Please try again.'" />
                    @endif
                    
                    <form method="POST" action="{{ route('login') }}" id="loginForm">
                        @csrf
                        
                        <x-form.input 
                            name="email" 
                            label="Email Address" 
                            type="email" 
                            :value="old('email')"
                            placeholder="Enter your email"
                            required
                        />
                        
                        <x-form.input 
                            name="password" 
                            label="Password" 
                            type="password" 
                            placeholder="Enter your password"
                            required
                        />
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>
                        
                        <x-form.button type="submit" variant="primary" class="w-100" icon="bi-box-arrow-in-right">
                            Sign In
                        </x-form.button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    @stack('scripts')
    <script src="{{ asset('js/auth/login.js') }}"></script>
</body>
</html>

