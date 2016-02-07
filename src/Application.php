<?php

namespace Loposum;

use Loposum\Command;

class Application extends \Symfony\Component\Console\Application
{
    const VERSION = 0.1;

    public function __construct()
    {
        parent::__construct('Loposum', self::VERSION);
    }

    /**
     * Gets the default commands that should always be available.
     *
     * @return array An array of default Command instances
     */
    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();
        $defaultCommands[] = new Command\TranslateCommand();
        return $defaultCommands;
    }
}
