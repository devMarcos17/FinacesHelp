<?php

use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\GoalsController;
use App\Http\Controllers\InvestimentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($r) {
    Route::post('/register', [UserController::class, 'register'])->name('register');
    Route::post('/login', [UserController::class, 'login'])->name('login');
    Route::post('/me', [UserController::class, 'me'])->middleware('auth')->name('me');
});

Route::group(['middleware' => 'api', 'prefix' => 'transaction'], function ($r) {
    Route::post('/createTransaction', [TransactionController::class, 'createTransaction'])->middleware('auth')->name('createTransaction');
    Route::post('/filter', [TransactionController::class, 'filterDate'])->middleware('auth')->name('filterDate');
    Route::post('/filterCategory', [TransactionController::class, 'filterCategory'])->middleware('auth')->name('filterCategory');

    Route::get('/listTransaction', [TransactionController::class, 'listTransaction'])->middleware('auth')->name('listTransaction');
    Route::get('/listTransaction', [TransactionController::class, 'listTransactionId'])->middleware('auth')->name('listTransactionId');
    Route::get('/balance', [TransactionController::class, 'balance'])->middleware('auth')->name('balance');
    Route::get('/totalExpense', [TransactionController::class, 'totalExpense'])->middleware('auth')->name('totalExpense');
    Route::get('/totalRevenue', [TransactionController::class, 'totalRevenue'])->middleware('auth')->name('totalRevenue');
    Route::get('/filterCategory', [TransactionController::class, 'filterCategory'])->middleware('auth')->name('filterCategory');
    Route::get('/expensesCategories', [TransactionController::class, 'expensesCategories'])->middleware('auth')->name('expensesCategories');
    Route::get('/expense', [TransactionController::class, 'expense'])->middleware('auth')->name('expense');
    Route::get('/filterRevenue', [TransactionController::class, 'filterRevenue'])->middleware('auth')->name('filterRevenue');
    Route::get('/filterExpense', [TransactionController::class, 'filterExpense'])->middleware('auth')->name('filterExpense');
    Route::get('/expensesCategories', [TransactionController::class, 'expensesCategories'])->middleware('auth')->name('expensesCategory');
    Route::get('/expensesByCategory', [TransactionController::class, 'expensesByCategory'])->middleware('auth')->name('expensesByCategory');
    Route::get('/monthlyRevenue', [TransactionController::class, 'monthlyRevenue'])->middleware('auth')->name('monthlyRevenue');
    Route::get('/monthlyExpense', [TransactionController::class, 'monthlyExpense'])->middleware('auth')->name('monthlyExpense');

    Route::put('/updateTransaction/{id}', [TransactionController::class, 'updateTransaction'])->middleware('auth')->name('updateTransaction');

    Route::delete('/deleteTransaction/{id}', [TransactionController::class, 'deleteTransaction'])->middleware('auth')->name('deleteTransaction');
});

Route::group(['middleware' => 'api', 'prefix' => 'dashboard'], function ($router) {
    Route::get('/dashboard', [DashBoardController::class, 'dashboard'])->middleware('auth')->name('dashboard');
});

Route::group(['middleware' => 'api', 'prefix' => 'goals'], function ($router) {
    
    Route::post('/create', [GoalsController::class, 'createGoal'])->middleware('auth')->name('createGoal');
    Route::post('/{id}/deposit', [GoalsController::class, 'deposit'])->middleware('auth')->name('deposit');

    Route::put('/update', [GoalsController::class, 'updateGoal'])->middleware('auth')->name('updateGoal');
    Route::delete('/delete', [GoalsController::class, 'deleteGoal'])->middleware('auth')->name('deleteGoal');
    Route::get('/list/{id}', [GoalsController::class, 'listGoal'])->middleware('auth')->name('listGoal');
    Route::get('/progress/{id}', [GoalsController::class, 'progress'])->middleware('auth')->name('progress');
});
Route::group(['middleware' => 'api', 'prefix' => 'investiment'], function($router){
    Route::post('/create',[InvestimentController::class, 'createInvestiment'])->middleware('auth')->name('createInvestiment');
    Route::post('/deposit/{id}',[InvestimentController::class, 'deposit'])->middleware('auth')->name('deposit');

    Route::put('/update/{id}',[InvestimentController::class, 'updateInvestiment'])->middleware('auth')->name('updateInvestiment');

    Route::get('/list',[InvestimentController::class, 'listInvestiments'])->middleware('auth')->name('listInvestiments');
    Route::get('/list/{id}',[InvestimentController::class, 'listInvestimentId'])->middleware('auth')->name('listInvestimentId');
    Route::get('/profitability/{id}',[InvestimentController::class, 'profitability'])->middleware('auth')->name('profitability');

    Route::delete('/delete/{id}',[InvestimentController::class, 'deleteInvestiment'])->middleware('auth')->name('deleteInvestiment  ');
});
