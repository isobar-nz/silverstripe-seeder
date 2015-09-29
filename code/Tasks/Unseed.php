<?php

use LittleGiant\SilverStripeSeeder\CliOutputFormatter;
use LittleGiant\SilverStripeSeeder\Util\Check;
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
        if (!Check::fileToUrlMapping()) {
            die('ERROR: Please set a valid path in $_FILE_TO_URL_MAPPING before running the seeder' . PHP_EOL);
        }

        // Customer overrides delete to check for admin

        // major hack to enable ADMIN permissions
        // login throws cookie warning, this will hide the error message
        error_reporting(0);
        try {
            if ($admin = Member::default_admin()) {
                $admin->logIn();
            }
        } catch (Exception $e) {
        }
        error_reporting(E_ALL);

        $seeder = new Seeder(new RecordWriter(), new CliOutputFormatter());

        $seeder->unseed();
    }
}
