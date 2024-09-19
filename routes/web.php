<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/', [\App\Http\Controllers\DishController::class, 'index'])->name('dishes.index');
    Route::get('/dishes/{slug}', [\App\Http\Controllers\DishController::class, 'show'])->name('dishes.show');
    Route::get('/edit/{slug}', [\App\Http\Controllers\DishController::class, 'edit'])->name('dishes.edit');
    Route::put('/dishes/{slug}', [\App\Http\Controllers\DishController::class, 'update'])->name('dishes.update');
    Route::get('/create', [\App\Http\Controllers\DishController::class, 'create'])->name('dishes.create');
    Route::post('/dishes/store', [\App\Http\Controllers\DishController::class, 'store'])->name('dishes.store');
    Route::delete('/{id}', [\App\Http\Controllers\DishController::class, 'delete'])->name('dishes.delete');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/users/{id}/favorite', [ProfileController::class, 'addFavoriteDishToUser'])->name('addFavoriteDishToUser');
});

require __DIR__.'/auth.php';
