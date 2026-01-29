<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Only admins can access dashboard
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('invoices.create');
        }
        
        return view('admin.dashboard.index');
    }
}

