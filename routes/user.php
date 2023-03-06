<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

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

Route::get('social-login/{provider}', [AuthController::class, 'socialLogin'])->name('social-login');
Route::get('social-login/{provider}/callback', [AuthController::class, 'socialLoginCallback'])->name('social-callback');

Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::get('verification/{token}', [AuthController::class, 'verification'])->name('verification');
Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('update-profile', [AuthController::class, 'updateProfile'])->name('update-profile');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});
