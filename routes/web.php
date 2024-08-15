<?php

use App\Http\Controllers\UserController;
use App\Livewire\Users\Create as UserCreate;
use App\Livewire\Users\Index as UsersIndex;
use App\Livewire\Users\Show as UserShow;
use App\Livewire\Users\Trashed as TrashedUsers;
use App\Livewire\Users\Update as UserUpdate;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';


Route::prefix('dashboard')->middleware(['auth'])->group(function () {

    Route::softDeletes('users', UserController::class);

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class,'index'])->name('index');
        Route::get('/create', [UserController::class,'create'])->name('create');
        Route::post('/', [UserController::class,'store'])->name('store');
        Route::delete('/{user}/destroy', [UserController::class,'destroy'])->name('destroy');
        Route::get('/{user}', [UserController::class,'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class,'edit'])->name('edit');
        Route::put('/{user}', [UserController::class,'update'])->name('update');
    });



});

