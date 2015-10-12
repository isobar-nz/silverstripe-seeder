<?php

namespace LittleGiant\SilverStripeSeeder\Providers;

use LittleGiant\SilverStripeSeeder\Util\Field;

abstract class Provider extends \Object
{
    protected $writer;

    private $dataObjectRecordProperty;

    abstract protected function generateField($field, $state);

    public function __construct()
    {
        parent::__construct();

        $this->dataObjectRecordProperty = new \ReflectionProperty('DataObject', 'record');
        $this->dataObjectRecordProperty->setAccessible(true);
    }

    public static function parseOptions($argumentString)
    {
        $options = array();
        $options['arguments'] = array_map(function ($arg) {
            return trim($arg);
        }, explode(',', $argumentString));
        return $options;
    }

    public function generate($field, $state)
    {
        $values = array();

        if (isset($field->arguments['nullable']) && $field->arguments['nullable']) {
            if (rand(0, 100) < 20) {
                return $values;
            }
        }

        if ($field->fieldType === Field::FT_FIELD) {
            $values[] = $this->generateField($field, $state);
        } else if ($field->fieldType === Field::FT_HAS_ONE) {
            $values[] = $this->generateHasOneField($field, $state);
        } else if ($field->fieldType === Field::FT_HAS_MANY
            || $field->fieldType === Field::FT_MANY_MANY
            || $field->fieldType === Field::FT_ROOT
        ) {
            $values = $this->generateHasManyField($field, $state);
        }

        return $values;
    }

    protected function generateObject($field, $upState, $index = 0)
    {
        $className = $field->dataType;
        $object = \QuickDataObject::create($className);

        $state = $upState->down($field, $object, $index);

        $fields = $this->dataObjectRecordProperty->getValue($object);
        foreach ($field->fields as $objectField) {
            if ($objectField->ignore) {
                continue;
            }

            $values = $objectField->provider->generate($objectField, $state);
            if (!empty($values)) {
                $fields[$objectField->fieldName] = $values[0];
            }
        }
        $this->dataObjectRecordProperty->setValue($object, $fields);

        $writer = $this->writer;


        $hasOneIsAncestor = false;
        $afterHasOneExists = new \OnAfterExists(function () use ($object, $field, $writer) {
            $writer->write($object, $field);
        });
        foreach ($field->hasOne as $hasOneField) {
            if ($hasOneField->ignore) {
                continue;
            }

            $values = $hasOneField->provider->generate($hasOneField, $state);
            if (!empty($values[0])) {
                $value = $values[0];
                $relation = $hasOneField->fieldName;

                $hasOneIsAncestor = $hasOneIsAncestor || $state->isAncestor($value);
                $afterHasOneExists->addCondition($values[0], function ($value) use ($object, $relation) {
                    // set has_one field ID
                    $fields = $this->dataObjectRecordProperty->getValue($value);
                    $objectFields = $this->dataObjectRecordProperty->getValue($object);
                    $objectFields[$relation] = $fields['ID'];
                    $this->dataObjectRecordProperty->setValue($object, $objectFields);
                });
            }
        }

        foreach ($field->manyMany as $manyManyField) {
            if ($manyManyField->ignore) {
                continue;
            }

            $values = $manyManyField->provider->generate($manyManyField, $state);
            if (!empty($values)) {
                $relation = $manyManyField->methodName;
                $afterExists = new \OnAfterExists(function () use ($object, $relation, $values, $writer) {
                    $writer->writeManyMany($object, $relation, $values);
                });
                $afterExists->addCondition($object);
                $afterExists->addCondition($values);
            }
        }

        foreach ($field->hasMany as $hasManyField) {
            if ($hasManyField->ignore) {
                continue;
            }

            $values = $hasManyField->provider->generate($hasManyField, $state);
            if (!empty($values)) {
                $object->onAfterExistsCallback(function ($object) use ($values, $writer, $hasManyField) {
                    $relation = '';
                    foreach ($values[0]->has_one() as $fieldName => $className) {
                        if ($object instanceof $className) {
                            $relation = $fieldName . 'ID';
                        }
                    }

                    if ($relation) {
                        $objectFields = $this->dataObjectRecordProperty->getValue($object);
                        $id = $objectFields['ID'];
                        foreach ($values as $value) {
                            // set has_many field to object ID
                            $fields = $this->dataObjectRecordProperty->getValue($value);
                            $fields[$relation] = $id;
                            $this->dataObjectRecordProperty->setValue($value, $fields);
                            $this->writer->write($value, $hasManyField);
                        }
                    }
                });
            }
        }

        // only write object when it won't already be written when has_one fields are written
        // unless there is a dependency write loop, then write first

        // don't write has many fields since they will be written once their owner is written
        if ((!$afterHasOneExists->count() || $hasOneIsAncestor) && $field->fieldType !== Field::FT_HAS_MANY) {
            $this->writer->write($object, $field);
        }

        return $object;
    }

    protected function generateHasOneField($field, $state)
    {
        $object = $this->generateObject($field, $state);
        return $object;
    }

    protected function generateHasManyField($field, $state)
    {
        $objects = array();
        for ($i = 0; $i < $field->count; $i++) {
            $objects[] = $this->generateObject($field, $state, $i);
        }
        return $objects;
    }

    public function get_shorthand()
    {
        return $this->config()->get('shorthand');
    }

    public function get_order()
    {
        return $this->config()->get('order');
    }

    public function setWriter($writer)
    {
        $this->writer = $writer;
    }
}
