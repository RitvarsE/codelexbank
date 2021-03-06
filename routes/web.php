<?php

use App\Http\Controllers\CreateAccountController;
use App\Http\Controllers\TransactionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])->get('/transaction', function () {
    return Inertia::render('Transactions/SendingTransaction');
})->name('transaction.create');

Route::get('/test', [TransactionController::class, 'transfer']);

Route::middleware(['auth:sanctum', 'verified'])->get('/receipt', function () {
    return Inertia::render('Transactions/Receipt');
})->name('receipt');

Route::middleware(['auth:sanctum', 'verified'])->get('/create', function () {
    return Inertia::render('Accounts/Create');
})->name('accounts.create');

Route::middleware(['auth:sanctum', 'verified'])->post('/create', [CreateAccountController::class, 'create'])
    ->name('accounts.created');

Route::get('error', function () {
    return Inertia::render('Errors/Error');
})->name('error');

Route::middleware(['auth:sanctum', 'verified'])->get('/transactions', function () {
    return Inertia::render('Transactions/History');
})->name('transactions.history');
Route::middleware(['auth:sanctum', 'verified'])->get('/buystock', function () {
    return Inertia::render('Stocks/BuyStock');
})->name('stocks.buy');

Route::middleware(['auth:sanctum', 'verified'])
    ->post('/validation', [TransactionController::class, 'validation'])
    ->name('transaction.send');

Route::middleware(['auth:sanctum', 'verified'])
    ->get('/yourStocks', function (){
        return Inertia::render('Stocks/YourStocks');
    })
    ->name('stocks.yourstocks');
