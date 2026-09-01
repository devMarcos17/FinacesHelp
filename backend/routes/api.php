<?php

use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\GoalsController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($r) {
    Route::post('/register', [UserController::class, 'register'])->name('register');
    Route::post('/login', [UserController::class, 'login'])->name('login');
});

Route::group(['middleware' => 'api', 'prefix' => 'transaction'], function ($r) {
    Route::post('/createTransaction', [TransactionController::class, 'createTransaction'])->middleware('auth')->name('createTransaction');
    Route::post('/filter', [TransactionController::class, 'filterDate'])->middleware('auth')->name('filterDate');
    Route::post('/filterCategory/{id}', [TransactionController::class, 'filterCategory'])->middleware('auth')->name('filterCategory');

    Route::get('/listTransaction', [TransactionController::class, 'listTransaction'])->middleware('auth')->name('listTransaction');
    Route::get('/listTransaction/{id}', [TransactionController::class, 'listTransactionId'])->middleware('auth')->name('listTransactionId');
    Route::get('/balance/{id}', [TransactionController::class, 'balance'])->middleware('auth')->name('balance');
    Route::get('/expense/{id}', [TransactionController::class, 'expense'])->middleware('auth')->name('expense');
    Route::get('/filterRevenue/{id}', [TransactionController::class, 'filterRevenue'])->middleware('auth')->name('filterRevenue');
    Route::get('/filterExpense/{id}', [TransactionController::class, 'filterExpense'])->middleware('auth')->name('filterExpense');
    Route::get('/expensesCategories/{id}', [TransactionController::class, 'expensesCategories'])->middleware('auth')->name('expensesCategory');
    Route::get('/expensesByCategory/{id}', [TransactionController::class, 'expensesByCategory'])->middleware('auth')->name('expensesByCategory');
    Route::get('/monthlyRevenue/{id}', [TransactionController::class, 'monthlyRevenue'])->middleware('auth')->name('monthlyRevenue');
    Route::get('/monthlyExpense/{id}', [TransactionController::class, 'monthlyExpense'])->middleware('auth')->name('monthlyExpense');

    Route::put('/updateTransaction/{id}', [TransactionController::class, 'updateTransaction'])->middleware('auth')->name('updateTransaction');

    Route::delete('/deleteTransaction/{id}', [TransactionController::class, 'deleteTransaction'])->middleware('auth')->name('deleteTransaction');
});

Route::group(['middleware' => 'api', 'prefix' => 'dashboard'], function ($router) {
    Route::get('/dashboard/{id}', [DashBoardController::class, 'dashboard'])->middleware('auth')->name('dashboard');
});

Route::group(['middleware' => 'api', 'prefix' => 'goals'], function ($router) {
    
    Route::post('/create', [GoalsController::class, 'createGoal'])->middleware('auth')->name('createGoal');
    Route::post('/{id}/deposit', [GoalsController::class, 'deposit'])->middleware('auth')->name('deposit');

    Route::put('/update', [GoalsController::class, 'updateGoal'])->middleware('auth')->name('updateGoal');
    Route::delete('/delete', [GoalsController::class, 'deleteGoal'])->middleware('auth')->name('deleteGoal');
    Route::get('/list/{id}', [GoalsController::class, 'listGoal'])->middleware('auth')->name('listGoal');
    Route::get('/progress/{id}', [GoalsController::class, 'progress'])->middleware('auth')->name('progress');
});
