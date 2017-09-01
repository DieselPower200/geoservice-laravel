<?php

namespace ADiesel82\SypexGeo;

class SypexGeoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/sypexgeo.php' => config_path('sypexgeo.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Register providers.
        $this->app['sypexgeo'] = $this->app->bindShared(function ($app) {
            $sypexConfig = $app['config'];
            $sypexConfigType = $sypexConfig->get('sypexgeo.sypexgeo.type', array());
            $sypexConfigPath = $sypexConfig->get('sypexgeo.sypexgeo.path', array());
            switch ($sypexConfigType) {
                case ('database'):
                    $sypexConfigFile = $sypexConfig->get('sypexgeo.sypexgeo.file', array());
                    $sxgeo = new SxGeo(base_path() . $sypexConfigPath . $sypexConfigFile);
                    break;
                case ('web_service'):
                    $license_key = $sypexConfig->get('sypexgeo.sypexgeo.license_key', array());
                    $sxgeo = new SxGeoHttp($license_key);
                    break;
                default:
                    $sypexConfigFile = $sypexConfig->get('sypexgeo.sypexgeo.file', array());
                    $sxgeo = new SxGeo(base_path() . $sypexConfigPath . $sypexConfigFile);
            }
            //return new GeoIP($app['config'], $app["session.store"]);
            return new SypexGeo($sxgeo, $app['config']);

            $this->app->bindShared('sypexgeo', function(Application $app) {
                $dbFile = \Config::get('sypexgeo::path');
                $service = $app->make('\ADiesel82\SypexGeo\SypexGeoServiceProvider', [$dbFile]);
                return $service;
            });

        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['sypexgeo'];
    }
}