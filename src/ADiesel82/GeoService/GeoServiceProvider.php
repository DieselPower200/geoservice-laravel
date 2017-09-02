<?php
namespace ADiesel82\GeoService;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class GeoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/geo.php' => config_path('geo.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('geo', function ($app) {
            return new GeoManager($app['config']->get('geo'));
        });

        $this->app->alias('geo', __NAMESPACE__.'\GeoManager');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['geo'];
    }
}