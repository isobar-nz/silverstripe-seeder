<?php

namespace Seeder\Tests;

use Seeder\Util\BatchedSeedWriter;
use Seeder\Util\Field;

/**
 * Class BatchSeedWriterTest
 * @package Seeder\Tests
 */
class BatchSeedWriterTest extends \SapphireTest
{
    /**
     * @var bool
     */
    protected $usesDatabase = true;

    /**
     * @var array
     */
    protected $extraDataObjects = array(
        'Seeder\Tests\Dog',
        'Seeder\Tests\House',
        'Seeder\Tests\Human',
        'Seeder\Tests\Pet',
        'Seeder\Tests\Treat',
    );

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setUpOnce();
    }

    /**
     *
     */
    public function testWrite_WriteObject_SeedAndObjectWritten()
    {
        $this->markTestIncomplete(
            'Need silverstripe-batchwrite compatibility.'
        );

        $batchSizes = array(10, 30, 100, 300);

        foreach ($batchSizes as $batchSize) {
            $writer = new BatchedSeedWriter($batchSize);

            $dog = new Dog();
            $dog->Name = 'Bob';
            $dog->Breed = 'Cavvy';

            $writer->write($dog, $this->createField());

            $writer->finish();

            $this->assertEquals(1, Dog::get()->Count());
            $this->assertEquals(1, \SeedRecord::get()->Count());

            $seed = \SeedRecord::get()->first();
            $dog = Dog::get()->first();

            $this->assertEquals('Seeder\Tests\Dog', $seed->SeedClassName);
            $this->assertEquals($dog->ID, $seed->SeedID);

            $seed->delete();
            $dog->delete();
        }
    }

    /**
     *
     */
    public function testWrite_WriteManyObjects_SeedsAndObjectsWritten()
    {
        $this->markTestIncomplete(
            'Need silverstripe-batchwrite compatibility.'
        );

        $batchSizes = array(10, 30, 100, 300);

        foreach ($batchSizes as $batchSize) {
            $writer = new BatchedSeedWriter($batchSize);

            for ($i = 0; $i < 100; $i++) {
                $dog = new Dog();
                $dog->Name = 'Bob' . $i;
                $dog->Breed = 'Cavvy' . $i;

                $owner = new Human();
                $owner->Name = 'Jim' . $i;

                $owner->onAfterExistsCallback(function ($owner) use ($dog, $writer) {
                    $dog->OwnerID = $owner->ID;
                    $writer->write($dog, $this->createField());
                });

                $writer->write($owner, $this->createField());
            }

            $writer->finish();

            $dogSeeds = \SeedRecord::get()->filter('SeedClassName', 'Seeder\Tests\Dog');
            $ownerSeeds = \SeedRecord::get()->filter('SeedClassName', 'Seeder\Tests\Human');
            $dogs = Dog::get();
            $owners = Human::get();

            $this->assertEquals(100, $dogs->Count());
            $this->assertEquals(100, $owners->Count());
            $this->assertEquals(100, $dogSeeds->Count());
            $this->assertEquals(100, $ownerSeeds->Count());


            for ($i = 0; $i < 100; $i++) {
                $dog = $dogs[$i];
                $owner = $owners[$i];
                $ownerSeed = $ownerSeeds[$i];
                $dogSeed = $dogSeeds[$i];

                $this->assertEquals('Seeder\Tests\Dog', $dogSeed->SeedClassName);
                $this->assertEquals($dog->ID, $dogSeed->SeedID);
                $this->assertEquals('Seeder\Tests\Human', $ownerSeed->SeedClassName);
                $this->assertEquals($owner->ID, $ownerSeed->SeedID);
            }

            $writer->delete($dogs);
            $writer->delete($dogSeeds);
            $writer->delete($owners);
            $writer->delete($ownerSeeds);
            $writer->finish();

            $this->assertEquals(0, Dog::get()->Count());
            $this->assertEquals(0, Human::get()->Count());
            $this->assertEquals(0, \SeedRecord::get()->Count());
        }
    }

    /**
     *
     */
    public function testWrite_WriteObjectsTwice_SeedsWrittenOnce()
    {
        $this->markTestIncomplete(
            'Need silverstripe-batchwrite compatibility.'
        );

        $batchSizes = array(10, 30, 100, 300);

        foreach ($batchSizes as $batchSize) {
            $writer = new BatchedSeedWriter($batchSize);

            for ($i = 0; $i < 100; $i++) {
                $dog = new Dog();
                $dog->Name = 'Shark' . $i;
                $dog->Age = $i;
                $dog->Breed = 'Blue Whale';

                $field = $this->createField();
                $writer->write($dog, $field);
                $writer->write($dog, $field);
            }

            $writer->finish();

            $this->assertEquals(100, Dog::get()->Count());
            $this->assertEquals(100, \SeedRecord::get()->Count());

            $dogs = Dog::get();
            $seeds = \SeedRecord::get();
            $writer->delete($dogs);
            $writer->delete($seeds);
            $writer->finish();
        }
    }

    /**
     *
     */
    public function testWrite_WriteVersionedObjectsNotPublished_ObjectsWrittenToStage()
    {
        $this->markTestIncomplete(
            'Need silverstripe-batchwrite compatibility.'
        );

        $batchSizes = array(10, 30, 100, 300);

        foreach ($batchSizes as $batchSize) {
            $writer = new BatchedSeedWriter($batchSize);

            for ($i = 0; $i < 100; $i++) {
                $page = new \SiteTree();
                $page->Title = 'Magical Unicorn Journeys ' . $i;

                $field = $this->createField();
                $field->options['publish'] = false;
                $writer->write($page, $field);
            }

            $writer->finish();

            $currentStage = \Versioned::current_stage();
            \Versioned::reading_stage('Stage');
            $this->assertEquals(100, \SiteTree::get()->Count());

            \Versioned::reading_stage('Live');
            $this->assertEquals(0, \SiteTree::get()->Count());

            \Versioned::reading_stage('Stage');
            $pages = \SiteTree::get();
            $seeds = \SeedRecord::get();
            $writer->deleteFromStage($pages, 'Stage', 'Live');
            $writer->delete($seeds);
            $writer->finish();

            \Versioned::reading_stage($currentStage);
        }
    }

    /**
     *
     */
    public function testWrite_WriteVersionedObjects_ObjectsWrittenToLive()
    {
        $this->markTestIncomplete(
            'Need silverstripe-batchwrite compatibility.'
        );

        $batchSizes = array(10, 30, 100, 300);

        foreach ($batchSizes as $batchSize) {
            $writer = new BatchedSeedWriter($batchSize);

            for ($i = 0; $i < 100; $i++) {
                $page = new \SiteTree();
                $page->Title = 'Magical Unicorn Journeys ' . $i;

                $field = $this->createField();
                $writer->write($page, $field);
            }

            $writer->finish();

            $currentStage = \Versioned::current_stage();
            \Versioned::reading_stage('Stage');
            $this->assertEquals(100, \SiteTree::get()->Count());

            \Versioned::reading_stage('Live');
            $this->assertEquals(100, \SiteTree::get()->Count());

            $pages = \SiteTree::get();
            $seeds = \SeedRecord::get();
            $writer->deleteFromStage($pages, 'Stage', 'Live');
            $writer->delete($seeds);
            $writer->finish();

            $this->assertEquals(0, \SiteTree::get()->Count());
            $this->assertEquals(0, \SeedRecord::get()->Count());

            \Versioned::reading_stage($currentStage);
        }
    }

    /**
     *
     */
    public function testWriteManyMany_WriteManyManyObjects_ObjectsAccessibleFromManyMany()
    {
        $this->markTestIncomplete(
            'Need silverstripe-batchwrite compatibility.'
        );

        $batchSizes = array(10, 30, 100, 300);

        foreach ($batchSizes as $batchSize) {
            $writer = new BatchedSeedWriter($batchSize);

            for ($i = 0; $i < 10; $i++) {
                $owner = new Human();
                $owner->Name = 'Mr bean ' . $i;

                for ($j = 0; $j < 10; $j++) {
                    $dog = new Dog();
                    $dog->Name = 'Walnut ' . $i;

                    $afterExists = new \OnAfterExists(function () use ($owner, $dog, $writer) {
                        $writer->writeManyMany($owner, 'Pets', $dog);
                    });
                    $afterExists->addCondition($owner);
                    $afterExists->addCondition($dog);

                    $writer->write($dog, $this->createField());
                }

                $writer->write($owner, $this->createField());
            }

            $writer->finish();

            $owners = Human::get();
            $dogs = Dog::get();
            $this->assertEquals(10, $owners->Count());
            $this->assertEquals(100, $dogs->Count());

            foreach ($owners as $owner) {
                $this->assertEquals(10, $owner->Pets()->Count());
            }

            $writer->delete($owners);
            $writer->delete($dogs);
            $writer->finish();
        }
    }

    /**
     * @return Field
     */
    private function createField()
    {
        $field = new Field();
        $field->key = 'test';
        return $field;
    }

//    /**
//     *
//     */
//    public static function tearDownAfterClass()
//    {
//        parent::tearDownAfterClass();
//        \SapphireTest::delete_all_temp_dbs();
//    }
}
