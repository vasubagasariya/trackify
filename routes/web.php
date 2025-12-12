<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;


Route::get('/',[LoginController::class,'show'])->name('login.show')->middleware('adminCheckLogout');
Route::post('/',[LoginController::class,'check'])->name('login.check')->middleware('adminCheckLogout');


Route::middleware('adminCheck')->group(function(){
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('accounts')->name('accounts.')->group(function(){
        Route::get('/',[AccountController::class, 'show'])->name('show');
        Route::post('store',[AccountController::class, 'store'])->name('store');
        Route::put('update/{name}',[AccountController::class, 'update'])->name('update');
        Route::delete('delete/{name}',[AccountController::class, 'delete'])->name('delete');
    });
    
    Route::prefix('transactions')->name('transactions.')->group(function(){
        Route::get('/',[TransactionController::class,'show'])->name('show');
        Route::post('store',[TransactionController::class, 'store'])->name('store');
        Route::post('update/{id}',[TransactionController::class, 'update'])->name('update');
        Route::delete('delete/{id}',[TransactionController::class, 'delete'])->name('delete');
    });

    Route::prefix('transfers')->name('transfers.')->group(function(){
        Route::get('/',[TransferController::class,'show'])->name('show');
        Route::post('store',[TransferController::class, 'store'])->name('store');
        Route::post('update/{id}',[TransferController::class, 'update'])->name('update');
        Route::delete('delete/{id}',[TransferController::class, 'delete'])->name('delete');
    });
    Route::get('/logout', function () {
        session()->flush('admin_logged_in');
        return redirect()->route('login.show');
    })->name('logout');

});