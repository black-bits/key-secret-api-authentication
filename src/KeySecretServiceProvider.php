<?php

namespace BlackBits\KeySecretApiAuthentication;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class KeySecretServiceProvider extends ServiceProvider
{

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        Auth::extend('key_secret', function ($app, $name, array $config) {
            return new KeySecretGuard(Auth::createUserProvider($config['provider']));
        });
    }
}
