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
                        <h2 class="mt-3 mb-1">{{ $otpPending ?? false ? 'Verify OTP' : 'Admin Login' }}</h2>
                        <p class="text-muted">{{ $otpPending ?? false ? 'Enter the OTP sent to your email' : 'Sign in to access the admin panel' }}</p>
                    </div>
                    
                    @if(session('otp_sent'))
                        <x-ui.alert type="success" :message="session('otp_sent')" />
                    @endif
                    
                    @if(session('error'))
                        <x-ui.alert type="danger" :message="session('error')" />
                    @endif
                    
                    @if($errors->any())
                        <x-ui.alert type="danger" :message="'Please fix the errors below.'" />
                    @endif
                    
                    @if($otpPending ?? false)
                        <!-- OTP Verification Form -->
                        <form method="POST" action="{{ route('login') }}" id="otpForm">
                            @csrf
                            
                            <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
                            <input type="hidden" name="remember" value="{{ old('remember') ? '1' : '0' }}">
                            
                            <div class="mb-3">
                                <label for="otp" class="form-label">Enter OTP</label>
                                <input 
                                    type="text" 
                                    class="form-control @error('otp') is-invalid @enderror" 
                                    id="otp" 
                                    name="otp" 
                                    placeholder="Enter 6-digit OTP"
                                    maxlength="6"
                                    pattern="[0-9]{6}"
                                    required
                                    autofocus
                                    autocomplete="off"
                                >
                                @error('otp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">OTP sent to: <strong>{{ $email ?? old('email') }}</strong></small>
                            </div>
                            
                            <x-form.button type="submit" variant="primary" class="w-100" icon="bi-shield-check">
                                Verify OTP
                            </x-form.button>
                            
                            <div class="mt-3 text-center">
                                <form method="POST" action="{{ route('resend-otp') }}" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
                                    <button type="submit" class="btn btn-link text-decoration-none p-0">
                                        <small>Didn't receive OTP? Resend</small>
                                    </button>
                                </form>
                            </div>
                            
                            <div class="mt-2 text-center">
                                <form method="GET" action="{{ route('cancel-otp') }}" style="display: inline;">
                                    <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
                                    <button type="submit" class="btn btn-link text-decoration-none p-0">
                                        <small>Back to Login</small>
                                    </button>
                                </form>
                            </div>
                        </form>
                    @else
                        <!-- Login Form -->
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
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    @stack('scripts')
    <script src="{{ asset('js/auth/login.js') }}"></script>
</body>
</html>

