<?php

namespace App\Http\Controllers;

use App\Services\ConverterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConverterController extends Controller
{
    private ConverterService $service;

    public function __construct(ConverterService $service)
    {
        $this->service = $service;
    }

    public function getCurrency(Request $request): JsonResponse
    {
        return response()->json($this->service->getCurrency($request));
    }

    public function getAllCurrencies(): JsonResponse
    {
        return response()->json($this->service->getAllCurrencies());
    }
}
