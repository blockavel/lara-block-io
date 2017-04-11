<?php

namespace Blockavel\LaraBlockIo;

use Illuminate\Support\Facades\Facade;

class LaraBlockIoFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'lara-block-io';
    }
}
