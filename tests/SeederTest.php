<?php

class SeederTest extends SapphireTest
{
    protected $usesDatabase = true;

    public function testSeed()
    {
        $seeder = new Seeder2();
        $seeder->seed();

        $this->assertEquals(Page::get()->count(), 10);
        $this->assertEquals(School::get()->count(), 5);
        $this->assertEquals(Principal::get()->count(), 5);
        $this->assertEquals(Classroom::get()->count(), 15);
        $this->assertEquals(Student::get()->count(), 75);

        $seeder->unseed();

        $this->assertEquals(Page::get()->count(), 0);
        $this->assertEquals(School::get()->count(), 0);
        $this->assertEquals(Principal::get()->count(), 0);
        $this->assertEquals(Classroom::get()->count(), 0);
        $this->assertEquals(Student::get()->count(), 0);
    }
}
