<?php

/**
 * Class DatabaseSeed
 */
class DatabaseSeed extends CliController
{
    /**
     *
     */
    function process()
    {
        $seeder = Seeder::create();
        $seeder->seed();
    }
}
