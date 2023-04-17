<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [Authcontroller::class, 'Register'])->name('register');
Route::post('/', [Authcontroller::class, 'postRegister'])->name('post.register');
Route::get('/verify/{token}', [AuthController::class, 'emailVerify'])->name('verify');
Route::get('login', [Authcontroller::class, 'Login'])->name('login');
Route::post('login', [Authcontroller::class, 'postLogin'])->name('post.login');
Route::middleware(['auth', 'email_verified'])->group(function(){
    Route::get('/dashboard', [Authcontroller::class, 'Dashboard'])->name('dashboard');
    Route::get('/logout', [AuthController::class, 'LogOut'])->name('logout');
});
