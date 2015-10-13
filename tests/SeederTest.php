<?php

use LittleGiant\SilverStripeSeeder\CliOutputFormatter;
use LittleGiant\SilverStripeSeeder\Util\BatchedSeedWriter;
use LittleGiant\SilverStripeSeeder\Util\RecordWriter;

class SeederTest extends SapphireTest
{
    protected $usesDatabase = true;

    public function testSeed()
    {
        $seeder = new Seeder(new BatchedSeedWriter(), new CliOutputFormatter());
        $seeder->seed();
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        SapphireTest::delete_all_temp_dbs();
    }
}
