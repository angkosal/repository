<?php

namespace Angkosal\Repository;

use Angkosal\Repository\Commands\MakeBindingCommand;
use Angkosal\Repository\Commands\MakeCriteriaCommand;
use Angkosal\Repository\Commands\MakeRepositoryCommand;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    private $repoCommands = [
        MakeCriteriaCommand::class,
        MakeRepositoryCommand::class,
        MakeBindingCommand::class,
    ];

    /**
     * Bootstrap services.
     */
    public function boot()
    {
    }

    /**
     * Register services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/repository.php', 'repository');

        $this->publishes([
            __DIR__.'/config/repository.php' => app()->basePath().'/config/repository.php',
        ], 'config');

        $this->registerCommands();
    }

    /**
     * Registers repoist commands.
     */
    public function registerCommands()
    {
        $this->commands($this->repoCommands);
    }
}
