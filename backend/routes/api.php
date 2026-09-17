<?php

use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\GoalsController;
use App\Http\Controllers\InvestimentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('/register', [UserController::class, 'register'])->name('register');
    Route::post('/login', [UserController::class, 'login'])->name('login');
    Route::post('/me', [UserController::class, 'me'])->middleware('auth')->name('me');
    Route::post('/logout', [UserController::class, 'logout'])->middleware('auth')->name('logout');
    Route::post('/disable', [UserController::class, 'disableUser'])->middleware(['auth', 'admin'])->name('disableUser');
    Route::post('/active', [UserController::class, 'activeUser'])->middleware(['auth', 'admin'])->name('activeUser');
    Route::post('/search', [UserController::class, 'search'])->middleware(['auth', 'admin'])->name('search');
    Route::post('/forgot', [UserController::class,'forgotPassword'])->name('forgot');
    Route::post('/reset', [UserController::class,'resetPassword'])->name('resetPassword');
    
    Route::get('/getActiveUsers', [UserController::class, 'getActiveUsers'])->middleware(['auth', 'admin'])->name('getActiveUsers');
    Route::get('/getDisableUsers', [UserController::class, 'getDisableUsers'])->middleware(['auth', 'admin'])->name('getDisableUsers');
    Route::get('/activeUsersTotal', [UserController::class, 'getActiveUsersTotal'])->middleware(['auth', 'admin'])->name('getActiveUsersTotal');
    Route::get('/disableUsersTotal', [UserController::class, 'getDisableUsersTotal'])->middleware(['auth', 'admin'])->name('getDisableUsersTotal');
    Route::get('/adminsTotal', [UserController::class, 'getAdminsTotal'])->middleware(['auth', 'admin'])->name('getAdminsTotal');
    Route::get('/usersTotal', [UserController::class, 'getUsersTotal'])->middleware(['auth', 'admin'])->name('getUsersTotal');
    Route::get('/list', [UserController::class, 'list'])->middleware(['auth','admin'])->name('list');
    Route::get('/filterUsersMonth', [UserController::class, 'filterUsersMonth'])->middleware(['auth', 'admin'])->name('filterUsersMonth');
    //Route::get('/list', [UserController::class, 'listId'])->middleware('auth')->name('listId');
    Route::get('/filterDataOld', [UserController::class, 'filterDataOld'])->middleware(['auth', 'admin'])->name('filterDataOld');
    Route::get('/filterDataRecent', [UserController::class, 'filterDataRecent'])->middleware(['auth', 'admin'])->name('filterDataRecent');
    
    
    Route::put('/update', [UserController::class, 'update'])->middleware(['auth', 'admin'])->name('update');

    Route::delete('/delete', [UserController::class, 'delete'])->middleware(['auth', 'admin'])->name('delete');
});

Route::group(['middleware' => 'api', 'prefix' => 'transaction'], function ($router) {
    Route::post('/createTransaction', [TransactionController::class, 'createTransaction'])->middleware('auth')->name('createTransaction');
    Route::post('/filter', [TransactionController::class, 'filterDate'])->middleware('auth')->name('filterDate');
    Route::post('/filterCategory', [TransactionController::class, 'filterCategory'])->middleware('auth')->name('filterCategory');
    Route::post('/expensesByCategory', [TransactionController::class, 'expensesByCategory'])->middleware('auth')->name('expensesByCategory');
    Route::post('/monthlyRevenue', [TransactionController::class, 'monthlyRevenue'])->middleware('auth')->name('monthlyRevenue');
    Route::post('/monthlyExpense', [TransactionController::class, 'monthlyExpense'])->middleware('auth')->name('monthlyExpense');

    Route::get('/listTransaction', [TransactionController::class, 'listTransaction'])->middleware('auth')->name('listTransaction');
    Route::get('/listTransaction', [TransactionController::class, 'listTransactionId'])->middleware('auth')->name('listTransactionId');
    Route::get('/balance', [TransactionController::class, 'balance'])->middleware('auth')->name('balance');
    Route::get('/totalExpense', [TransactionController::class, 'totalExpense'])->middleware('auth')->name('totalExpense');
    Route::get('/totalRevenue', [TransactionController::class, 'totalRevenue'])->middleware('auth')->name('totalRevenue');
    Route::get('/filterCategory', [TransactionController::class, 'filterCategory'])->middleware('auth')->name('filterCategory');
    Route::get('/expense', [TransactionController::class, 'expense'])->middleware('auth')->name('expense');
    Route::get('/filterRevenue', [TransactionController::class, 'filterRevenue'])->middleware('auth')->name('filterRevenue');
    Route::get('/filterExpense', [TransactionController::class, 'filterExpense'])->middleware('auth')->name('filterExpense');

    Route::put('/update', [TransactionController::class, 'updateTransaction'])->middleware('auth')->name('updateTransaction');

    Route::delete('/delete', [TransactionController::class, 'deleteTransaction'])->middleware('auth')->name('deleteTransaction');
});

Route::group(['middleware' => 'api', 'prefix' => 'dashboard'], function ($router) {
    Route::get('/dashboard', [DashBoardController::class, 'dashboard'])->middleware('auth')->name('dashboard');
});

Route::group(['middleware' => 'api', 'prefix' => 'goals'], function ($router) {
    
    Route::post('/create', [GoalsController::class, 'createGoal'])->middleware('auth')->name('createGoal');
    Route::post('/deposit', [GoalsController::class, 'deposit'])->middleware('auth')->name('deposit');

    Route::put('/update', [GoalsController::class, 'updateGoal'])->middleware('auth')->name('updateGoal');
   
    Route::delete('/delete', [GoalsController::class, 'deleteGoal'])->middleware('auth')->name('deleteGoal');
   
    Route::get('/list', [GoalsController::class, 'listGoalUser'])->middleware('auth')->name('listGoalUser');
    Route::post('/progress', [GoalsController::class, 'progress'])->middleware('auth')->name('progress');
});
Route::group(['middleware' => 'api', 'prefix' => 'investiment'], function($router){
    Route::post('/create',[InvestimentController::class, 'createInvestiment'])->middleware('auth')->name('createInvestiment');
    //Route::post('/deposit',[InvestimentController::class, 'deposit'])->middleware('auth')->name('deposit');

    Route::put('/update',[InvestimentController::class, 'updateInvestiment'])->middleware('auth')->name('updateInvestiment');

    Route::get('/list',[InvestimentController::class, 'listInvestiment'])->middleware('auth')->name('listInvestiments');
    Route::get('/list',[InvestimentController::class, 'listInvestimentId'])->middleware('auth')->name('listInvestimentId');
    Route::post('/profitability',[InvestimentController::class, 'profitability'])->middleware('auth')->name('profitability');

    Route::delete('/delete',[InvestimentController::class, 'deleteInvestiment'])->middleware('auth')->name('deleteInvestiment');

});
