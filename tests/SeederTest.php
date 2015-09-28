<?php

use LittleGiant\SilverStripeSeeder\CliOutputFormatter;
use LittleGiant\SilverStripeSeeder\Util\RecordWriter;

class SeederTest extends SapphireTest
{
    protected $usesDatabase = true;

    public function testSeed()
    {
        $seeder = new Seeder(new RecordWriter(), new CliOutputFormatter());
        $seeder->seed();

        $this->assertEquals(Page::get()->count(), 4);

        $seeder->unseed();

        $this->assertEquals(Page::get()->count(), 0);
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        SapphireTest::delete_all_temp_dbs();
    }
}
