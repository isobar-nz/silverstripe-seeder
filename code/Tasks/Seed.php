<?php

use LittleGiant\SilverStripeSeeder\CliOutputFormatter;
use LittleGiant\SilverStripeSeeder\Util\Check;
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

        global $argv;

        $seeder = new Seeder(new RecordWriter(), new CliOutputFormatter());

        $className = null;
        $key = null;

        $nextClass = false;
        $nextKey = false;
        foreach ($argv as $arg) {
            if ($nextClass) {
                if (class_exists($arg)) {
                    $className = $arg;
                } else {
                    die("class '{$arg}' does not exist" . PHP_EOL);
                }
                $nextClass = false;
            }
            if ($nextKey) {
                $key = $arg;
                $nextKey = false;
            }

            if ($arg === '-f' || $arg === '--force') {
                $seeder->setIgnoreSeeds(true);
            }
            if ($arg === '-k' || $arg === '--key') {
                $nextKey = true;
            }
            if ($arg === '-c' || $arg === '--class') {
                $nextClass = true;
            }
        }

        $seeder->seed($className, $key);
    }
}
