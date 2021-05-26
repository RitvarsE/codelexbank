<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StockController extends Controller
{
    private StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function getQuote(Request $request): string
    {
        return $this->stockService->getQuote($request);
    }

    public function buyStock(Request $request): RedirectResponse
    {
        return $this->stockService->buyStock($request);
    }

    public function getAll(Request $request): JsonResponse
    {
        return response()->json($this->stockService->getAll($request));
    }

    public function sellStock(Stock $stock)
    {
        return $this->stockService->sellStock($stock);
    }
}
