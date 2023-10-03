<?php

use Illuminate\Support\Facades\Route;

// ====================Artisan command======================
Route::middleware('auth:admin')->group(function () {
    Route::get('route-clear', function () {
        \Artisan::call('route:clear');
        dd('Route Cleared');
    });

    Route::get('optimize', function () {
        \Artisan::call('optimize');
        dd('Optimized');
    });

    Route::get('optimize-clear', function () {
        \Artisan::call('optimize:clear');

        flashSuccess('Cache cleared successfully');

        return back();
    })->name('app.optimize-clear');

    Route::get('view-clear', function () {
        \Artisan::call('view:clear');
        dd('View Cleared');
    });

    Route::get('config-clear', function () {
        \Artisan::call('config:clear');
        dd('configuration cleared again');
    });
});

Route::get('migrate/data', function () {
    \Artisan::call('migrate');

    session()->flash('success', 'Migrated Successfully');

    return redirect()->route('website.home');
});
