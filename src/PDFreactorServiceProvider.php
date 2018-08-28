<?php

namespace StepStone\PDFreactor;

use Illuminate\Support\ServiceProvider;

class PDFreactorServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('pdfreactor', function () {
            return new PDFreactor(
                config('services.pdfreactor.host'),
                config('services.pdfreactor.port'),
                config('services.pdfreactor.key')
            );
        });
    }
}