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
        $seeder = new Seeder(new RecordWriter(), new CliOutputFormatter());

        global $argv;

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
