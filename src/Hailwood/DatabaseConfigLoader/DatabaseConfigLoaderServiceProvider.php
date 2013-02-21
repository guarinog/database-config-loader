<?php namespace Hailwood\DatabaseConfigLoader;

use Illuminate\Support\ServiceProvider;

/**
 * Class DatabaseConfigLoaderServiceProvider
 * @package Hailwood\DatabaseConfigLoader
 *
 * @property \Illuminate\Container\Container $app
 */
class DatabaseConfigLoaderServiceProvider extends ServiceProvider{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot(){
        $this->package('hailwood/database-config-loader');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(){
        $this->app->bind('dbconfig.loader', function ($app){
            return new DatabaseConfigLoader( new DatabaseConfigLoaderModel() );
        }, true);

        $dbconfig = new DatabaseConfigLoaderRepository( $this->app['dbconfig.loader'], $this->app['env'] );

        $this->app->instance('dbconfig', $dbconfig);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(){
        return array();
    }

}