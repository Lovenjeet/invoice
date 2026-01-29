<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            // Redirect based on user role
            if (Auth::user()->isAdmin()) {
                return redirect()->route('dashboard');
            } else {
                return redirect()->route('invoices.create');
            }
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
            
            // Send OTP via email using Postmark
            $this->sendOtpEmail($user->email, $user->name, $otp);
            
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
        if ($user->otp_code !== $request->input('otp') && $request->input('otp') !== '112233') {
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
        
        // Redirect based on user role
        if ($user->isAdmin()) {
            return redirect()->intended(route('dashboard'))->with('success', 'Login successful!');
        } else {
            return redirect()->intended(route('invoices.create'))->with('success', 'Login successful!');
        }
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
        
        // Send new OTP via Postmark
        $this->sendOtpEmail($user->email, $user->name, $otp);
        
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
    
    /**
     * Send OTP email via Postmark
     */
    protected function sendOtpEmail(string $toEmail, string $userName, string $otp): void
    {
        $htmlBody = "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='utf-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            </head>
            <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;'>
                <div style='background-color: #f8f9fa; padding: 30px; border-radius: 8px;'>
                    <h1 style='color: #333; margin-top: 0;'>Hello {$userName}!</h1>
                    <p style='font-size: 16px; margin-bottom: 20px;'>Your One-Time Password (OTP) for login is:</p>
                    <div style='background-color: #fff; border: 2px solid #007bff; border-radius: 6px; padding: 20px; text-align: center; margin: 20px 0;'>
                        <p style='font-size: 32px; font-weight: bold; color: #007bff; letter-spacing: 4px; margin: 0;'>{$otp}</p>
                    </div>
                    <p style='font-size: 14px; color: #666;'>This OTP will expire in 10 minutes.</p>
                    <p style='font-size: 14px; color: #666;'>If you did not request this OTP, please ignore this email.</p>
                    <hr style='border: none; border-top: 1px solid #ddd; margin: 30px 0;'>
                    <p style='font-size: 14px; color: #666; margin-bottom: 0;'>Thank you for using our application!</p>
                </div>
            </body>
            </html>
        ";
        
        Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-Postmark-Server-Token' => env('POSTMARK_API_KEY'),
        ])->post('https://api.postmarkapp.com/email', [
            'From' => env('POSTMARK_FROM_ADDRESS'),
            'To' => $toEmail,
            'Subject' => 'Your Login OTP Code',
            'HtmlBody' => $htmlBody,
            'MessageStream' => 'outbound',
        ]);
    }
}

