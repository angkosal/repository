<?php

namespace Angkosal\Repository;

use Angkosal\Repository\Commands\MakeCriterionCommand;
use Angkosal\Repository\Commands\MakeRepositoryCommand;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    private $repoCommands = [
        MakeCriterionCommand::class,
        MakeRepositoryCommand::class,
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
        $this->mergeConfigFrom(__DIR__.'/config/angkosal-repo.php', 'angkosal-repo');

        $this->publishes([
            __DIR__.'/config/angkosal-repo.php' => app()->basePath().'/config/angkosal-repo.php',
        ], 'angkosal-repo-config');

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
