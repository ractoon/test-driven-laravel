<?php

namespace App\Providers;

use App\OrderConfirmationNumberGenerator;
use App\RandomOrderConfirmationNumberGenerator;
use App\Billing\PaymentGateway;
use App\Billing\StripePaymentGateway;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(StripePaymentGateway::class, function () {
            return new StripePaymentGateway(config('services.stripe.secret'));
        });

        $this->app->bind(PaymentGateway::class, StripePaymentGateway::class);
        $this->app->bind(OrderConfirmationNumberGenerator::class, RandomOrderConfirmationNumberGenerator::class);
    }
}
