<?php

namespace LittleGiant\SilverStripeSeeder\Util;

class RecordWriter
{
    public function write(\DataObject $object, Field $field)
    {
        if ($object->has_extension('Versioned')) {
            $object->writeToStage('Stage');

            $args = $field->arguments;
            $publish = isset($args['publish']) ? $args['publish'] : true;

            if ($publish) {
                $object->publish('Stage', 'Live');
            }
        } else {
            $object->write();
        }

        $seed = new \Seed();
        $seed->SeedClassName = $object->ClassName;
        $seed->SeedID = $object->ID;
        $seed->write();
    }

    public function finish()
    {

    }
}
