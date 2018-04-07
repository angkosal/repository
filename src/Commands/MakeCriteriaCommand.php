<?php

namespace Angkosal\Repository\Commands;

class MakeCriteriaCommand extends RepositoryCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:criteria {criteria}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new criteria';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->createCriteria();
    }

    /**
     * Create a new criteria.
     */
    protected function createCriteria()
    {
        $content = $this->fileManager->get(
            __DIR__.'/../stubs/Criteria/Example.stub'
        );

        $criteria = $this->argument('criteria');

        $replacements = [
            '%namespaces.criteria%' => $this->appNamespace.$this->config('namespaces.criteria'),
            '%criteria%' => $criteria,
        ];

        $content = str_replace(array_keys($replacements), array_values($replacements), $content);

        $fileName = $criteria;
        $fileDirectory = app()->basePath().'/app/'.$this->config('paths.criteria');
        $filePath = $fileDirectory.'/'.$fileName.'.php';

        if (!$this->fileManager->exists($fileDirectory)) {
            $this->fileManager->makeDirectory($fileDirectory, 0755, true);
        }

        if ($this->laravel->runningInConsole() && $this->fileManager->exists($filePath)) {
            $response = $this->ask("The criteria [{$fileName}] already exists. Do you want to overwrite it?", 'Yes');

            if (!$this->isResponsePositive($response)) {
                $this->line("The criteria [{$fileName}] will not be overwritten.");

                return;
            }

            $this->fileManager->put($filePath, $content);
        } else {
            $this->fileManager->put($filePath, $content);
        }

        $this->line("The criteria [{$fileName}] has been created.");
    }
}
