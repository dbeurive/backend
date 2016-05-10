#!/usr/bin/env php
<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR. 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use dbeurive\Backend\Database\Connector\Option as ConnectorOption;


/**
 * Class MySql
 *
 * This class implements the CLI adaptor for the MySql database.
 * Please note that this class does only one thing: set options for the CLI.
 *
 * @package dbeurive\Backend\Cli\Adapter\Database\DocWriter
 */
class MySql extends Command {

    /**
     * @see \Symfony\Component\Console\Command\Command
     */
    protected function configure() {
        $this->setName('db:mysql')
             ->setDescription("Extract the configuration for MySql.");
    }

    /**
     * This method is called by the Symfony's console class.
     * It prints the shell's configuration for the CLI tool.
     * @see Symfony\Component\Console\Command\Command
     * @param InputInterface $input Input interface.
     * @param OutputInterface $output Output interface.
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $config = require __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

        $script = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR. 'src' . DIRECTORY_SEPARATOR . 'Cli' . DIRECTORY_SEPARATOR . 'Bin' . DIRECTORY_SEPARATOR . 'backend';

        $all = $config['application'];

        $options = [$script, "\tdb:doc-mysql", "\t-v"];
        foreach ($all as $_name => $_value) {
            if (0 == strlen($_value)) {
                continue;
            }
            $t = sprintf("\t--%s=%s", $_name, $_value);
            $t = str_replace('\\', '\\\\', $t);
            $options[] = $t;
        }

        $output->writeln(implode(" \\\n", $options));
    }
}

$application = new Application();
$application->setAutoExit(true);
$application->add(new MySql());
$application->run();
