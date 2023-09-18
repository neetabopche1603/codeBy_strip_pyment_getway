<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlanController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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



Route::get('register', [AuthController::class, 'registerView'])->name('registerView');
Route::post('register', [AuthController::class, 'register'])->name('registerPost');

Route::get('login', [AuthController::class, 'loginView'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('loginPost');


Route::get('/', [PlanController::class, 'index'])->name('plans');
Route::get('/checkout/{id}', [PlanController::class, 'checkout'])->name('checkout')->middleware('auth');
Route::get('/success', [PlanController::class, 'paymentSuccess'])->name('success');
Route::get('/cancel', [PlanController::class, 'paymentCancel'])->name('cancel');


Route::get('/logout', function () {
    Auth::logout();
    return redirect()->route('login');
})->name('logout');
