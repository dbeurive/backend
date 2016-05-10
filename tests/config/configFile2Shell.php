#!/usr/bin/env php
<?php

/**
 * This file implements a simple tool that eases the task of writing long command lines for testing the script.
 */

namespace dbeurive\BackendTest\config;

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR. 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Class MySqlDoc
 * @package dbeurive\BackendTest\config
 */
class MySqlDoc extends Command {
    /**
     * @see \Symfony\Component\Console\Command\Command
     */
    protected function configure() {
        $this->setName('db:doc-mysql')
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
        $script = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR. 'src' . DIRECTORY_SEPARATOR . 'Cli' . DIRECTORY_SEPARATOR . 'Bin' . DIRECTORY_SEPARATOR . 'backend';
        $loader = new MySqlDocConfLoader();
        $config = $loader->load();

        $options = [$script, "\tdb:doc-mysql", "\t-v"];
        foreach ($config as $_name => $_value) {
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

/**
 * Class MySqlSchema
 * @package dbeurive\BackendTest\config
 */
class MySqlSchema extends Command {
    /**
     * @see \Symfony\Component\Console\Command\Command
     */
    protected function configure() {
        $this->setName('db:schema-mysql')
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
        $script = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR. 'src' . DIRECTORY_SEPARATOR . 'Cli' . DIRECTORY_SEPARATOR . 'Bin' . DIRECTORY_SEPARATOR . 'backend';
        $loader = new MySqlSchemaConfLoader();
        $config = $loader->load();

        $options = [$script, "\tdb:schema-mysql", "\t-v"];
        foreach ($config as $_name => $_value) {
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
$application->add(new MySqlDoc());
$application->add(new MySqlSchema());
$application->run();
