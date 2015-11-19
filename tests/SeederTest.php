<?php

use Seeder\Helpers\CliOutputFormatter;
use Seeder\Util\BatchedSeedWriter;

/**
 * Class SeederTest
 */
class SeederTest extends SapphireTest
{
    /**
     * @var bool
     */
    protected $usesDatabase = true;

//    /**
//     * @throws Exception
//     */
//    public function testSeed()
//    {
//        $seeder = new Seeder(new BatchedSeedWriter(), new CliOutputFormatter());
//        $seeder->seed();
//    }

//    /**
//     *
//     */
//    public static function tearDownAfterClass()
//    {
//        parent::tearDownAfterClass();
//        SapphireTest::delete_all_temp_dbs();
//    }
}
