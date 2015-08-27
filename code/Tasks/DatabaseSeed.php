<?php

/**
 * Class DatabaseSeed
 */
class DatabaseSeed extends CliController
{
    /**
     *
     */
    function process()
    {
        $seeder = Seeder::create();

        global $argv;

        foreach ($argv as $arg) {
            if ($arg === '-i' || $arg === '--ignore') {
                $seeder->ignoreCurrentRecords(true);
            }
        }

        $seeder->seed();
    }
}
