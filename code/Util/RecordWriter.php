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

        if (!$object->isSeeded()) {
            $seed = new \SeedRecord();
            $seed->SeedClassName = $object->ClassName;
            $seed->SeedID = $object->ID;
            $seed->Key = $field->key;

            $seed->Root = $field->fieldType === Field::FT_ROOT;

            $seed->write();

            $object->setIsSeeded();
        }
    }

    public function writeManyMany($object, $relation, $manyManyObjects)
    {
        $object->$relation()->addMany($manyManyObjects);
    }

    public function delete($objects)
    {
        foreach ($objects as $object) {
            $object->delete();
        }
    }

    public function deleteIDs($className, $ids)
    {
        foreach ($ids as $id) {
            $object = $className::get()->byID($id);
            if ($object) {
                $object->delete();
            }
        }
    }

    public function deleteFromStage($objects, $stage)
    {
        $stages = array_slice(func_get_args(), 1);
        foreach ($objects as $object) {
            foreach ($stages as $stage) {
                $object->deleteFromStage($stage);
            }
        }
    }

    public function deleteIDsFromStage($className, $ids, $stage)
    {
        $stages = array_slice(func_get_args(), 2);
        foreach ($ids as $id) {
            $object = $className::get()->byID($id);

            if ($object) {
                foreach ($stages as $stage) {
                    $object->deleteFromStage($stage);
                }
            }
        }
    }

    public function finish()
    {

    }
}
