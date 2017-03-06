<?php

    namespace Blockavel\Blockavel;
    
    use Illuminate\Support\Facades\Facade;
    
    class BlockavelFacade extends Facade
    {
        protected static function getFacadeAccessor() { 
            return 'blockavel-blockavel';
        }
    }

?>