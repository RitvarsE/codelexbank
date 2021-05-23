<?php

namespace App\Console\Commands;

use App\Services\ConverterService;
use Illuminate\Console\Command;

class updateCurrencyDatabase extends Command
{
    protected $signature = 'converter:update';

    protected $description = 'Update converter database';

    private ConverterService $service;

    public function __construct(ConverterService $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    public function handle()
    {
        $this->service->upload();
        echo 'Update completed';
    }
}
