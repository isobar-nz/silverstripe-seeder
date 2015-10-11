<?php

namespace LittleGiant\SilverStripeSeeder\Util;

class BatchedSeedWriter
{
    private $batchWriter;

    private $isVersioned = array();

    private $dataObjectRecordProperty;

    public function __construct($batchSize = 100)
    {
        $this->batchWriter = new \BatchedWriter($batchSize);

        $this->dataObjectRecordProperty = new \ReflectionProperty('DataObject', 'record');
        $this->dataObjectRecordProperty->setAccessible(true);
    }

    public function write(\DataObject $object, Field $field)
    {
        $className = $object->class;

        // cache has_extension call
        if (!isset($this->isVersioned[$className])) {
            $this->isVersioned[$className] = $object->has_extension('Versioned');
        }

        if ($this->isVersioned[$className]) {
            $args = $field->arguments;

            if (!isset($args['publish']) || $args['publish']) {
                $this->batchWriter->writeToStage($object, 'Stage', 'Live');
            } else {
                $this->batchWriter->writeToStage($object, 'Stage');
            }
        } else {
            $this->batchWriter->write($object);
        }

        $isSeeded = $object->isSeeded();
        if (!$isSeeded) {
            $isRoot = $field->fieldType === Field::FT_ROOT;

            $object->onAfterExistsCallback(function ($object) use($field, $isRoot) {
                $seed = \QuickDataObject::create('SeedRecord');

                $objectFields = $this->dataObjectRecordProperty->getValue($object);
                $fields = $this->dataObjectRecordProperty->getValue($seed);
                $fields['SeedClassName'] = $object->class;
                $fields['SeedID'] = $objectFields['ID'];
                $fields['Key'] = $field->key;
                $fields['Root'] = $isRoot;

                $this->dataObjectRecordProperty->setValue($seed, $fields);

                $this->batchWriter->write($seed);
            });

            $object->setIsSeeded();
        }
    }

    public function writeManyMany($object, $relation, $manyManyObjects)
    {
        $this->batchWriter->writeManyMany($object, $relation, $manyManyObjects);
    }

    public function delete($objects)
    {
        $this->batchWriter->delete($objects);
    }

    public function deleteIDs($className, $ids)
    {
        $this->batchWriter->deleteIDs($className, $ids);
    }

    public function deleteFromStage($objects, $stage)
    {
        $stages = array_slice(func_get_args(), 1);
        foreach ($stages as $stage) {
            $this->batchWriter->deleteFromStage($objects, $stage);
        }
    }

    public function deleteIDsFromStage($className, $ids, $stage)
    {
        $stages = array_slice(func_get_args(), 2);
        foreach ($stages as $stage) {
            $this->batchWriter->deleteIDsFromStage($className, $ids, $stage);
        }
    }

    public function finish()
    {
        $this->batchWriter->finish();
    }
}
