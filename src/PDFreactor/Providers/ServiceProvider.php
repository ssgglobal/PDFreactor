<?php

namespace StepStone\PDFreactor\Providers;

use Illuminate\Support\ServiceProvider as BaseProvider;
use StepStone\PDFreactor\PDFreactor;

class ServiceProvider extends BaseProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/pdfreactor.php'  => config_path('pdfreactor.php'),
        ]);
    }

    public function register()
    {
        $this->app->singleton('pdfreactor', function () {
            return new PDFreactor(
                config('pdfreactor.host'),
                config('pdfreactor.port'),
                config('pdfreactor.key')
            );
        });
    }
}