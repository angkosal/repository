<?php

namespace Angkosal\Repository\Commands;

use Illuminate\Console\Command;

class RepositoryCommand extends Command
{
    /**
     * File manager.
     *
     * @var Illuminate\Filesystem\Filesystem
     */
    protected $fileManager;

    /**
     * Application namespace.
     *
     * @var string
     */
    protected $appNamespace;

    public function __construct()
    {
        parent::__construct();

        $this->fileManager = app('files');
        $this->appNamespace = app()->getNamespace();
    }

    /**
     * Determine if the user input is positive.
     *
     * @param  string
     * @param mixed $response
     *
     * @return bool
     */
    public function isResponsePositive($response)
    {
        return in_array(strtolower($response), ['y', 'yes'], true);
    }

    /**
     * Gets a configuration from package config file.
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function config($key)
    {
        return config('repository.'.$key);
    }
}
