<?php

use LittleGiant\SilverStripeSeeder\CliOutputFormatter;
use LittleGiant\SilverStripeSeeder\Util\RecordWriter;

/**
 * Class Unseed
 */
class Unseed extends CliController
{
    protected $title = "Unseed the database";

    protected $description = "Remove seeded database rows.";

    /**
     *
     */
    function process()
    {
        $seeder = new Seeder(new RecordWriter(), new CliOutputFormatter());

        $seeder->unseed();
    }
}
