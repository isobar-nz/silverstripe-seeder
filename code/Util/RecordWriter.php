<?php

namespace LittleGiant\SilverStripeSeeder\Util;

class RecordWriter
{
    public function write(\DataObject $object)
    {
        $object->write();
        $seed = new \Seed();
        $seed->SeedClassName = $object->ClassName;
        $seed->SeedID = $object->ID;
        $seed->write();
    }

    public function finish()
    {

    }
}
