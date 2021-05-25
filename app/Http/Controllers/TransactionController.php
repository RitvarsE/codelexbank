<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    private TransactionService $service;

    public function __construct(TransactionService $service)
    {
        $this->service = $service;
    }

    public function validation(Request $request): RedirectResponse
    {
        return $this->service->validation($request);
    }

    public function validateVerification(Request $request): RedirectResponse
    {
        return $this->service->validateVerification($request);
    }

    public function receipt(Request $request): JsonResponse
    {
        return response()->json($this->service->receipt($request));
    }

    public function sendMoney(Request $request)
    {
        return $this->service->sendMoney($request);
    }

    public function transactionHistory(Request $request): JsonResponse
    {
        return response()->json($this->service->transactionHistory($request));
    }
}
