<?php

namespace LittleGiant\SilverStripeSeeder;

use LittleGiant\SilverStripeSeeder\Util\CounterTree;

interface OutputFormatter
{
    public function beginSeed();
    public function creatingDataObject($className);
    public function dataObjectsCreated($className, $count);
    public function reportDataObjectsCreated(CounterTree $tree);
    public function beginUnseed();
    public function reportDataObjectsDeleted($deleted);
}
