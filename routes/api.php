<?php

use App\Http\Controllers\ConverterController;
use App\Http\Controllers\GetAccountController;
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

Route::get('/getReceipt/{id}', [TransactionController::class, 'receipt'])->middleware('auth:sanctum');

Route::get('/getCurrencies/', [ConverterController::class, 'getAllCurrencies'])->middleware('auth:sanctum');

Route::get('/history', [TransactionController::class, 'transactionHistory'])->middleware('auth:sanctum');
