<?php

namespace Amcysoft\Scaffold;

use Illuminate\Support\ServiceProvider;

class ScaffoldServiceProvider extends ServiceProvider
{
    protected $commands = [
        'Amcysoft\Scaffold\Commands\MakeScaffoldCommand',
        'Amcysoft\Scaffold\Commands\RemoveScaffoldCommand'
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'scaffold');

        $this->publishes([
            __DIR__.'/../lang' => resource_path('lang/vendor/scaffold'),
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\Collective\Html\HtmlServiceProvider::class);
        $this->app->register(\Laracasts\Flash\FlashServiceProvider::class);

        $loader = \Illuminate\Foundation\AliasLoader::getInstance();

        $loader->alias('Form', '\Collective\Html\FormFacade');
        $loader->alias('Html', '\Collective\Html\HtmlFacade');
        $loader->alias('Flash', '\Laracasts\Flash\Flash');

        $this->commands($this->commands);
    }
}
