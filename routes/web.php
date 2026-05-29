<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;



// web routes
Route::get('/', function () {
    return view('screens.web.home.index');
});



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// dashboard routes
Route::get('/admin', function () {
    return view('screens.admin.dashboard.index');
});

require __DIR__.'/auth.php';
