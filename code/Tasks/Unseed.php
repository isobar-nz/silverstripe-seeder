<?php

use LittleGiant\SilverStripeSeeder\CliOutputFormatter;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
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
        $app = new Application();
        $app->add(new UnseedCommand());
        $app->run();

    }
}

class UnseedCommand extends Command
{
    protected function configure()
    {
        $this->setName('unseed')
        ->setDescription('Unseed database')
        ->addOption('key', 'k', InputOption::VALUE_OPTIONAL, 'Choose key to unseed');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
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

        $key = null;

        $nextKey = false;
        foreach ($argv as $arg) {
            if ($nextKey) {
                $key = $arg;
                $nextKey = false;
            }

            if ($arg === '-k' || $arg === '--key') {
                $nextKey = true;
            }
        }

        $seeder->unseed($key);
    }
}
