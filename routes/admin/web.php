<?php

use App\Actions\Fortify\AdminAuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('guest')
    ->group(function(){
        Route::get('login', [AdminAuthenticatedSessionController::class, 'create'])
            ->name('login');
        Route::post('login', [AdminAuthenticatedSessionController::class, 'store']);
    });

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'role:admin'
])->group(function () {
    Route::post('logout', [AdminAuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
});
