<?php

use LittleGiant\SilverStripeSeeder\CliOutputFormatter;
use LittleGiant\SilverStripeSeeder\Util\RecordWriter;

/**
 * Class Seed
 */
class Seed extends CliController
{
    protected $title = "Seed the database";

    protected $description = "Populate the database with placeholder content.";

    /**
     *
     */
    function process()
    {
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

        global $argv;

        $seeder = new Seeder(new RecordWriter(), new CliOutputFormatter());

        $className = null;

        $next = false;
        foreach ($argv as $arg) {
            if ($next) {
                if (class_exists($arg)) {
                    $className = $arg;
                } else {
                    die("class '{$arg}' does not exist" . PHP_EOL);
                }
                $next = false;
            }

            if ($arg === '-f' || $arg === '--force') {
                $seeder->setIgnoreSeeds(true);
            }
            if ($arg === '-c' || $arg === '--class') {
                $next = true;
            }
        }

        $seeder->seed($className);
    }
}
