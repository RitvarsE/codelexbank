<?php

use App\Http\Controllers\ConverterController;
use App\Http\Controllers\GetAccountController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/getBankAccount/', [GetAccountController::class, 'get'])->middleware('auth:sanctum');

Route::get('/getUserAccounts/', [GetAccountController::class, 'getUserAccounts'])->middleware('auth:sanctum');

Route::get('/getAccountByNumber/', [GetAccountController::class, 'getAccountByNumber'])->middleware('auth:sanctum');

Route::get('/getReceipt/', [TransactionController::class, 'receipt'])->middleware('auth:sanctum');

Route::get('/getCurrencies/', [ConverterController::class, 'getAllCurrencies'])->middleware('auth:sanctum');

Route::get('/history', [TransactionController::class, 'transactionHistory'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'verified'])->post('/validation', [TransactionController::class, 'validation'])
    ->name('transaction.validate');

Route::middleware(['auth:sanctum', 'verified'])->post('/validateVerification', [TransactionController::class, 'validateVerification'])
    ->name('transaction.validateVerification');

Route::middleware(['auth:sanctum', 'verified'])->post('/sendMoney', [TransactionController::class, 'sendMoney'])
    ->name('transaction.sending');

Route::get('/getQuote/', [StockController::class, 'getQuote'])->middleware('auth:sanctum');

Route::post('/buyStock/', [StockController::class, 'buyStock'])->middleware('auth:sanctum');

Route::get('/getAllQuotes/', [StockController::class, 'getAll'])->middleware('auth:sanctum');

Route::delete('/sellStock/{stock}', [StockController::class, 'sellStock'])->middleware('auth:sanctum');
