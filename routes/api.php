<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\RegisterUserAdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');
    return ['Application has been cleared'];
});

Route::group(['prefix' => 'client'], function () {
    Route::get('/', [ClienteController::class, 'listAll'])->name('listAll');
    Route::get('/{id}', [ClienteController::class, 'show'])->name('show');
    Route::get('/create', [ClienteController::class, 'create'])->name('create');
    Route::patch('/update/{id}', [ClienteController::class, 'update'])->name('update');
    Route::delete('delete/{id}', [ClienteController::class, 'destroy'])->name('destroy');
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('/', LoginController::class)->name('login');
});

Route::group(['prefix' => 'register'], function () {
    Route::post('/', RegisterController::class)->name('register');
    Route::post('/user', RegisterUserAdminController::class)->name('registerUsers');
});
