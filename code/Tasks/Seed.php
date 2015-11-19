<?php

use Seeder\Helpers\CliOutputFormatter;
use Seeder\Util\BatchedSeedWriter;
use Seeder\Util\Check;
use Seeder\Util\RecordWriter;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Seed
 */
class Seed extends CliController
{
    /**
     * @var string
     */
    protected $title = "Seed the database";

    /**
     * @var string
     */
    protected $description = "Populate the database with placeholder content.";

    /**
     *
     */
    function process()
    {
        $app = new Application();
        $app->add(new SeedCommand());
        $app->run();
    }
}

/**
 * Class SeedCommand
 */
class SeedCommand extends Command
{
    /**
     *
     */
    protected function configure()
    {
        $this->setName('seed')
            ->setDescription('Seed database')
            ->addArgument('flush', InputArgument::OPTIONAL, 1)
            ->addOption('class', 'c', InputOption::VALUE_REQUIRED, 'Choose class to seed')
            ->addOption('key', 'k', InputOption::VALUE_REQUIRED, 'Choose key to seed')
            ->addOption('batch', 'b', InputOption::VALUE_NONE, 'Batch writes for better performance')
            ->addOption('size', 's', InputOption::VALUE_OPTIONAL, 'Specify batch size', 100)
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Ignore current seeds when calculating how many to create');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws Exception
     * @returns null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Check::fileToUrlMapping()) {
            die('ERROR: Please set a valid path in $_FILE_TO_URL_MAPPING before running the seeder' . PHP_EOL);
        }

        if (SiteTree::has_extension('SiteTreeLinkTracking')) {
            SiteTree::remove_extension('SiteTreeLinkTracking');
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

        $writer = new RecordWriter();
        if ($input->getOption('batch')) {
            $batchSize = intval($input->getOption('size'));
            $writer = new BatchedSeedWriter($batchSize);
        }

        $seeder = new Seeder($writer, new CliOutputFormatter());

        $className = $input->getOption('class');
        $key = $input->getOption('key');

        if ($input->getOption('force')) {
            $seeder->setIgnoreSeeds(true);
        }

        $seeder->seed($className, $key);

        return;
    }
}
