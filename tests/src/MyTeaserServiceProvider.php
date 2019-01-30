<?php

namespace Mplacegit\Teaser;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

/**
 * Class LaravelFilemanagerServiceProvider.
 */
class MyTeaserServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
              include __DIR__ . '/routes.php';
	          #$this->publishes([
              #__DIR__ . '/../config/mp-statistica.php' => config_path('mp-statistica.php'),
              #], 'config');
              $this->loadViewsFrom(__DIR__.'/../views', 'mp-teaser');

		  
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() 
    {
        #$this->mergeConfigFrom(__DIR__ . '/../config/mp-statistica.php', 'mp-statistica');
		#$this->app->singleton('mp-stat', function ($app) {
        #    return $app->make(Advertise::class);
        #});
		#$this->app->singleton('mp-advertstat', function ($app) {
        #    return $app->make(AdvertStat::class);
        #});
    }
}
