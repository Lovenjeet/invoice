<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeliveryBoyController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RechargeOfferController;
use App\Http\Controllers\Admin\BillToController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ShipToController;
use App\Http\Controllers\Admin\ShipperController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Auth routes (public)
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::prefix('users')->group(function(){
        Route::get('/', [UserController::class,'index'])->name('users.index');
        Route::get('/create', [UserController::class,'create'])->name('users.create');
        Route::post('/', [UserController::class,'store'])->name('users.store');
        Route::get('/{user}/edit', [UserController::class,'edit'])->name('users.edit');
        Route::get('/{user}', [UserController::class,'show'])->name('users.show');
        Route::put('/{user}', [UserController::class,'update'])->name('users.update');
        Route::delete('/{user}', [UserController::class,'destroy'])->name('users.destroy');
    });
    
    Route::prefix('shippers')->group(function(){
        Route::get('/', [ShipperController::class,'index'])->name('shippers.index');
        Route::get('/create', [ShipperController::class,'create'])->name('shippers.create');
        Route::post('/', [ShipperController::class,'store'])->name('shippers.store');
        Route::get('/{shipper}/edit', [ShipperController::class,'edit'])->name('shippers.edit');
        Route::get('/{shipper}', [ShipperController::class,'show'])->name('shippers.show');
        Route::put('/{shipper}', [ShipperController::class,'update'])->name('shippers.update');
        Route::delete('/{shipper}', [ShipperController::class,'destroy'])->name('shippers.destroy');
    });
    
    Route::prefix('bill-tos')->group(function(){
        Route::get('/', [BillToController::class,'index'])->name('bill-tos.index');
        Route::get('/create', [BillToController::class,'create'])->name('bill-tos.create');
        Route::post('/', [BillToController::class,'store'])->name('bill-tos.store');
        Route::get('/{billTo}/edit', [BillToController::class,'edit'])->name('bill-tos.edit');
        Route::get('/{billTo}', [BillToController::class,'show'])->name('bill-tos.show');
        Route::put('/{billTo}', [BillToController::class,'update'])->name('bill-tos.update');
        Route::delete('/{billTo}', [BillToController::class,'destroy'])->name('bill-tos.destroy');
    });
    
    Route::prefix('ship-tos')->group(function(){
        Route::get('/', [ShipToController::class,'index'])->name('ship-tos.index');
        Route::get('/create', [ShipToController::class,'create'])->name('ship-tos.create');
        Route::post('/', [ShipToController::class,'store'])->name('ship-tos.store');
        Route::get('/{shipTo}/edit', [ShipToController::class,'edit'])->name('ship-tos.edit');
        Route::get('/{shipTo}', [ShipToController::class,'show'])->name('ship-tos.show');
        Route::put('/{shipTo}', [ShipToController::class,'update'])->name('ship-tos.update');
        Route::delete('/{shipTo}', [ShipToController::class,'destroy'])->name('ship-tos.destroy');
    });
    
    Route::prefix('invoices')->group(function(){
        Route::get('/create', [InvoiceController::class,'create'])->name('invoices.create');
        Route::post('/', [InvoiceController::class,'store'])->name('invoices.store');
        Route::get('/{invoice}/edit', [InvoiceController::class,'edit'])->name('invoices.edit');
        Route::put('/{invoice}', [InvoiceController::class,'update'])->name('invoices.update');
        Route::get('/{invoice}', [InvoiceController::class,'show'])->name('invoices.show');
    });
    
});

