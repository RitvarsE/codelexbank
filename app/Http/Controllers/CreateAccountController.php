<?php

namespace App\Http\Controllers;

use App\Services\CreateAccountService;
use Illuminate\Http\Request;

class CreateAccountController extends Controller
{
    private CreateAccountService $service;

    public function __construct(CreateAccountService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request)
    {
        return $this->service->create($request);
    }
}
