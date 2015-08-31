<?php

use LittleGiant\SilverStripeSeeder\CliOutputFormatter;

/**
 * Class DatabaseUnseed
 */
class DatabaseUnseed extends CliController
{
    /**
     *
     */
    function process()
    {
        $seeder = Seeder::create(new CliOutputFormatter());

        $seeder->unseed();
    }
}
