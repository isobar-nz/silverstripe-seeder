<?php

class SeederTest extends SapphireTest
{
    protected $usesDatabase = true;

    public function testSeed()
    {
        $seeder = new Seeder2();
        $seeder->seed();

        $this->assertEquals(School::get()->count(), 5);

        $seeder->unseed();

        $this->assertEquals(School::get()->count(), 0);
    }
}
