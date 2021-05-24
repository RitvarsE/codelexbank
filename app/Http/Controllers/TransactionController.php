<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TransactionController extends Controller
{
    private TransactionService $service;

    public function __construct(TransactionService $service)
    {
        $this->service = $service;
    }

    public function transfer(Request $request)
    {
        return $this->service->transfer($request);
    }

    public function receipt(int $id): JsonResponse
    {
        return response()->json($this->service->receipt($id));
    }

    public function transactionHistory(Request $request): JsonResponse
    {
        return response()->json($this->service->transactionHistory($request));
    }
}
