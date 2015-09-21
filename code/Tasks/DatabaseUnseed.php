<?php

use LittleGiant\SilverStripeSeeder\CliOutputFormatter;

/**
 * Class DatabaseUnseed
 */
class DatabaseUnseed extends CliController
{
    protected $title = "Unseed the database";

    protected $description = "Remove seeded database rows.";

    /**
     *
     */
    function process()
    {
        $seeder = Seeder::create(new CliOutputFormatter());

        $seeder->unseed();
    }
}
