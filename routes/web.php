<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('/home', [TransactionController::class, 'index'])->name('transaction.index');
Route::get('/deposit', [TransactionController::class, 'showDeposits'])->name('transaction.showDeposits');
Route::post('/deposit', [TransactionController::class, 'deposit'])->name('transaction.deposit');
Route::get('/withdrawal', [TransactionController::class, 'showWithdrawals'])->name('transaction.showWithdrawals');
Route::post('/withdrawal', [TransactionController::class, 'withdrawal'])->name('transaction.withdrawal');
