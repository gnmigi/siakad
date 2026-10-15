<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Rules\TurnstileRule;

class ValidationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register custom Turnstile validation rule
        Validator::extend('turnstile', function ($attribute, $value, $parameters, $validator) {
            $rule = new TurnstileRule();
            $passes = true;
            
            $rule->validate($attribute, $value, function ($message) use (&$passes) {
                $passes = false;
            });
            
            return $passes;
        });

        // Set custom error message for turnstile validation
        Validator::replacer('turnstile', function ($message, $attribute, $rule, $parameters) {
            return 'Please complete the CAPTCHA verification.';
        });
    }
}
