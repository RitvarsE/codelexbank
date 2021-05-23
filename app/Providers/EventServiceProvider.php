<?php

namespace App\Providers;

use App\Models\BankAccount;
use App\Models\User;
use App\Services\CreateAccountService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        User::created(function ($user) {
            $service = new CreateAccountService();
            BankAccount::create([
                'user_id' => $user->id,
                'number' => $service->bankAccount(),
                'type' => 0,
                'currency' => 'EUR']);
        });
    }
}
