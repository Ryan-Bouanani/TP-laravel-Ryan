<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/', [\App\Http\Controllers\PlatController::class, 'index'])->name('plats.index');
    Route::get('/plats/{slug}', [\App\Http\Controllers\PlatController::class, 'show'])->name('plats.show');
    Route::get('/edit/{slug}', [\App\Http\Controllers\PlatController::class, 'edit'])->name('plats.edit');
    Route::put('/plats/{slug}', [\App\Http\Controllers\PlatController::class, 'update'])->name('plats.update');
    Route::get('/create', [\App\Http\Controllers\PlatController::class, 'create'])->name('plats.create');
    Route::post('/plats/store', [\App\Http\Controllers\PlatController::class, 'store'])->name('plats.store');
    Route::delete('/{id}', [\App\Http\Controllers\PlatController::class, 'delete'])->name('plats.delete');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/users/{id}/favorite', [ProfileController::class, 'addFavoritePlatToUser'])->name('addFavoritePlatToUser');
});

require __DIR__.'/auth.php';
