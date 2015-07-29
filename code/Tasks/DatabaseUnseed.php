<?php

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
        $seeder = Seeder::create();
        $seeder->unseed();
    }
}