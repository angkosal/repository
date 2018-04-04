<?php

namespace Angkosal\Repository\Commands;

use File;
use Illuminate\Console\Command;

class MakeBindingCommand extends RepositoryCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:binding {repository}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add repository bindings to service provider.';

    protected $providerDist = '';
    protected $providerName = 'RepositoryServiceProvider';
    protected $bindPlaceholder = '//:end-bindings:';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        $this->providerDist = app()->basePath().'/app/Providers/'.$this->providerName.'.php';
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->addRepositoryToProvider();
    }

    public function addRepositoryToProvider()
    {
        $this->makeProviderIfNotExist();

        if ($this->isBinded()) {
            return;
        }

        $provider = File::get($this->providerDist);
        $repositoryInterface = '\\'.$this->getRepository().'::class';
        $repositoryEloquent = '\\'.$this->getEloquentRepository().'::class';

        File::put($this->providerDist, str_replace($this->bindPlaceholder, "\$this->app->bind({$repositoryInterface}, {$repositoryEloquent});".PHP_EOL.'        '.$this->bindPlaceholder, $provider));

        $this->info('Binding have been added to RepositoryServiceProvider created successfully.');
    }

    public function isBinded()
    {
        $provider = File::get($this->providerDist);
        if (strpos($provider, $this->getRepository())) {
            return true;
        }

        return false;
    }

    public function makeProviderIfNotExist()
    {
        if (!file_exists($this->providerDist)) {
            $this->call('make:provider', [
                'name' => $this->providerName,
            ]);
            // placeholder to mark the place in file where to prepend repository bindings
            $provider = File::get($this->providerDist);
            File::put($this->providerDist, vsprintf(str_replace('//', '%s', $provider), [
                '//',
                $this->bindPlaceholder,
            ]));
        }
    }

    public function getRepository()
    {
        return app()->getNamespace().$this->config('namespaces.contracts').'\\'.
          str_replace('/', '\\', $this->argument('repository')).'Repository';
    }

    public function getEloquentRepository()
    {
        $repository = str_replace('/', '\\', $this->argument('repository'));
        $repo_ex = explode('\\', $repository);

        $repositoryName = array_pop($repo_ex);

        $prefixName = '';
        if (count($repo_ex) > 0) {
            $prefixName = implode('\\', $repo_ex).'\\';
        }

        return app()->getNamespace().$this->config('namespaces.repositories').'\\'.
        $prefixName.'Eloquent'.$repositoryName.'Repository';
    }
}
