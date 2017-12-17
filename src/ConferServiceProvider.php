<?php

namespace Tpojka\Confer;

use Push;
use Illuminate\Support\Facades\View;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class ConferServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('push', function($app) {
            $pusherCreds = $app['config']->get('broadcasting.connections.pusher');
            return new Push($pusherCreds['key'], $pusherCreds['secret'], $pusherCreds['app_id'], $pusherCreds['options']);
        });
        AliasLoader::getInstance()->alias('Push', 'Tpojka\Confer\Facades\Push');
        View::composer('confer::confer', 'Tpojka\Confer\Http\ViewComposers\ConferComposer');
        View::composer('confer::barconversationlist', 'Tpojka\Confer\Http\ViewComposers\ConferBarComposer');
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        //if ($this->app->runningInConsole()) return false;
        include __DIR__ . '/Http/routes.php';

        $this->loadViewsFrom(__DIR__ . '/views', 'confer');

        $this->publishes([
        	__DIR__ . '/views' => base_path('resources/views/vendor/confer'),
            __DIR__ . '/database/migrations/' => database_path('/migrations'),
            __DIR__ . '/database/seeds/' => database_path('/seeds'),
        	__DIR__ . '/config/confer.php' => config_path('confer.php'),
        	__DIR__ . '/assets/' => public_path('vendor/confer'),
        ]);

        $this->publishes([
            __DIR__ . '/database/migrations/' => database_path('/migrations')
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/database/seeds/' => database_path('/seeds')
        ], 'seeds');

        $this->publishes([
        	__DIR__ . '/views' => base_path('resources/views/vendor/confer'),
        ], 'views');

        $this->publishes([
            __DIR__ . '/assets/' => public_path('vendor/confer'),
        ], 'public');

        $this->publishes([
        	__DIR__ . '/config/confer.php' => config_path('confer.php'),
        ], 'config');
    }

}
