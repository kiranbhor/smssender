<?php
namespace Accunity\SMSSender\Facades;

use Illuminate\Support\Facades\Facade;

class SMS extends Facade {
    /**
     * Get the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'SMSConfig'; // the IoC binding.
    }
}   