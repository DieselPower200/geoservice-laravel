<?php
namespace ADiesel82\GeoService;

use Illuminate\Support\Facades\Facade;

class GeoServiceFacade extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'sypexgeo';
    }
}