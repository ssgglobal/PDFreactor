<?php

namespace StepStone\PDFreactor\Facades;

use Illuminate\Support\Facades\Facade;

class PDFreactor extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'pdfreactor';
    }
}