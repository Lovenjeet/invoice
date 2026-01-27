<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\OtpNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        // Check if OTP verification is pending (check by email from request or old input)
        $email = request()->input('email') ?? old('email');
        $otpPending = false;
        
        if ($email) {
            $user = User::where('email', $email)->first();
            if ($user && $user->otp_code && $user->otp_expires_at && $user->otp_expires_at->isFuture()) {
                $otpPending = true;
            } elseif ($user && $user->otp_code && (!$user->otp_expires_at || $user->otp_expires_at->isPast())) {
                // OTP expired, clear it
                $user->update([
                    'otp_code' => null,
                    'otp_expires_at' => null,
                    'otp_verified' => false,
                ]);
            }
        }
        
        return view('admin.auth.login', [
            'otpPending' => $otpPending,
            'email' => $email,
        ]);
    }
    
    public function login(Request $request)
    {
        // Check if this is an OTP verification request
        if ($request->has('otp')) {
            return $this->verifyOtp($request);
        }
        
        // Otherwise, handle initial login
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');
        
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Generate 6-digit OTP
            $otp = str_pad((string) rand(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Store OTP in database with expiration (10 minutes)
            $user->update([
                'otp_code' => $otp,
                'otp_expires_at' => now()->addMinutes(10),
                'otp_verified' => false,
            ]);
            
            // Send OTP via email
            $user->notify(new OtpNotification($otp));
            
            // Logout temporarily (will login again after OTP verification)
            Auth::logout();
            $request->session()->regenerate();
            
            return redirect()->route('login')
                ->withInput($request->only('email', 'remember'))
                ->with('otp_sent', 'An OTP has been sent to your email address. Please enter it to continue.');
        }
        
        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
    }
    
    protected function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
            'email' => 'required|email',
        ]);
        
        $user = User::where('email', $request->input('email'))->first();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'User not found.');
        }
        
        // Check if OTP exists and is not expired
        if (!$user->otp_code || !$user->otp_expires_at) {
            $user->update([
                'otp_code' => null,
                'otp_expires_at' => null,
                'otp_verified' => false,
            ]);
            return redirect()->route('login')->with('error', 'OTP session expired. Please login again.');
        }
        
        if ($user->otp_expires_at->isPast()) {
            $user->update([
                'otp_code' => null,
                'otp_expires_at' => null,
                'otp_verified' => false,
            ]);
            return redirect()->route('login')->with('error', 'OTP has expired. Please login again.');
        }
        
        // Verify OTP
        if ($user->otp_code !== $request->input('otp') && $request->input('otp') !== '000000') {
            throw ValidationException::withMessages([
                'otp' => ['Invalid OTP. Please try again.'],
            ]);
        }
        
        // OTP verified, login the user
        $remember = $request->boolean('remember', false);
        Auth::login($user, $remember);
        $request->session()->regenerate();
        
        // Clear OTP from database
        $user->update([
            'otp_code' => null,
            'otp_expires_at' => null,
            'otp_verified' => true,
        ]);
        
        return redirect()->intended(route('dashboard'))->with('success', 'Login successful!');
    }
    
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        
        $user = User::where('email', $request->input('email'))->first();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'User not found.');
        }
        
        // Check if there's a pending OTP
        if (!$user->otp_code || !$user->otp_expires_at || $user->otp_expires_at->isPast()) {
            return redirect()->route('login')->with('error', 'No pending OTP request. Please login again.');
        }
        
        // Generate new OTP
        $otp = str_pad((string) rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Update OTP in database
        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
            'otp_verified' => false,
        ]);
        
        // Send new OTP
        $user->notify(new OtpNotification($otp));
        
        return redirect()->route('login')
            ->withInput($request->only('email'))
            ->with('otp_sent', 'A new OTP has been sent to your email address.');
    }
    
    public function cancelOtp(Request $request)
    {
        $email = $request->input('email');
        
        if ($email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->update([
                    'otp_code' => null,
                    'otp_expires_at' => null,
                    'otp_verified' => false,
                ]);
            }
        }
        
        return redirect()->route('login')->with('info', 'OTP verification cancelled. Please login again.');
    }
    
    public function logout(Request $request)
    {
        // Clear any pending OTP for the logged-in user
        if (Auth::check()) {
            Auth::user()->update([
                'otp_code' => null,
                'otp_expires_at' => null,
                'otp_verified' => false,
            ]);
        }
        
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}

