<?php

namespace LittleGiant\SilverStripeSeeder\Util;

/**
 * Class BatchedSeedWriter
 * @package LittleGiant\SilverStripeSeeder\Util
 */
class BatchedSeedWriter
{
    /**
     * @var \BatchedWriter
     */
    private $batchWriter;

    /**
     * @var array
     */
    private $isVersioned = array();

    /**
     * @var \ReflectionProperty
     */
    private $dataObjectRecordProperty;

    /**
     * @param int $batchSize
     */
    public function __construct($batchSize = 100)
    {
        $this->batchWriter = new \BatchedWriter($batchSize);

        $this->dataObjectRecordProperty = new \ReflectionProperty('DataObject', 'record');
        $this->dataObjectRecordProperty->setAccessible(true);
    }

    /**
     * @param \DataObject $object
     * @param Field $field
     */
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

            $dataObjectProperty = $this->dataObjectRecordProperty;
            $batchWriter = $this->batchWriter;

            $object->onAfterExistsCallback(function ($object) use($field, $isRoot, $dataObjectProperty, $batchWriter) {
                $seed = \QuickDataObject::create('SeedRecord');

                $objectFields = $dataObjectProperty->getValue($object);
                $fields = $dataObjectProperty->getValue($seed);
                $fields['SeedClassName'] = $object->class;
                $fields['SeedID'] = $objectFields['ID'];
                $fields['Key'] = $field->key;
                $fields['Root'] = $isRoot;

                $dataObjectProperty->setValue($seed, $fields);

                $batchWriter->write($seed);
            });

            $object->setIsSeeded();
        }
    }

    /**
     * @param $object
     * @param $relation
     * @param $manyManyObjects
     */
    public function writeManyMany($object, $relation, $manyManyObjects)
    {
        $this->batchWriter->writeManyMany($object, $relation, $manyManyObjects);
    }

    /**
     * @param $objects
     */
    public function delete($objects)
    {
        $this->batchWriter->delete($objects);
    }

    /**
     * @param $className
     * @param $ids
     */
    public function deleteIDs($className, $ids)
    {
        $this->batchWriter->deleteIDs($className, $ids);
    }

    /**
     * @param $objects
     * @param $stage
     */
    public function deleteFromStage($objects, $stage)
    {
        $stages = array_slice(func_get_args(), 1);
        foreach ($stages as $stage) {
            $this->batchWriter->deleteFromStage($objects, $stage);
        }
    }

    /**
     * @param $className
     * @param $ids
     * @param $stage
     */
    public function deleteIDsFromStage($className, $ids, $stage)
    {
        $stages = array_slice(func_get_args(), 2);
        foreach ($stages as $stage) {
            $this->batchWriter->deleteIDsFromStage($className, $ids, $stage);
        }
    }

    /**
     *
     */
    public function finish()
    {
        $this->batchWriter->finish();
    }
}
