<?php

namespace App\Http\Controllers;

use App\Services\GetAccountService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GetAccountController extends Controller
{
    private GetAccountService $service;

    public function __construct(GetAccountService $service)
    {
        $this->service = $service;
    }

    public function getAccountByNumber(Request $request): JsonResponse
    {
        $account = $request->get('account');
        return response()->json($this->service->getAccountByNumber($account));
    }

    public function getUserAccounts(Request $request): JsonResponse
    {
        return response()->json($this->service->getUserAccounts($request));
    }
}
