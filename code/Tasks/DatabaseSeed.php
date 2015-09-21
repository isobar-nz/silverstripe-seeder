<?php

use LittleGiant\SilverStripeSeeder\CliOutputFormatter;

/**
 * Class DatabaseSeed
 */
class DatabaseSeed extends CliController
{
    protected $title = "Seed the database";

    protected $description = "Populate the database with placeholder content.";

    /**
     *
     */
    function process()
    {
        $seeder = Seeder::create(new CliOutputFormatter());

        global $argv;

        foreach ($argv as $arg) {
            if ($arg === '-i' || $arg === '--ignore') {
                $seeder->ignoreCurrentRecords(true);
            }
        }

        $seeder->seed();
    }
}
