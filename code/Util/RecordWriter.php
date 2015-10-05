<?php

namespace LittleGiant\SilverStripeSeeder\Util;

class RecordWriter
{
    private $tree;

    public function __construct()
    {
        $this->tree = new CounterTree();
    }

    public function write(\DataObject $object, Field $field, SeederState $state, $forceRecord = false)
    {
        $record = $forceRecord || !$object->exists();

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

        if ($record) {
            $seed = new \SeedRecord();
            $seed->SeedClassName = $object->ClassName;
            $seed->SeedID = $object->ID;
            $seed->Key = $field->key;

            $ancestry = $state->getClassAncestry();
            $seed->Root = count($ancestry) === 1;

            $seed->write();

            $this->tree->record($ancestry);
        }
    }

    public function getTree()
    {
        return $this->tree;
    }

    public function finish()
    {

    }
}
