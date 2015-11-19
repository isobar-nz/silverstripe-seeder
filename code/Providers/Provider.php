<?php

namespace Seeder;

use Seeder\Util\Field;

/**
 * Class Provider
 * @package Seeder
 */
abstract class Provider extends \Object
{
    /**
     * @var
     */
    protected $writer;

    /**
     * @param $field
     * @param $state
     * @return mixed
     */
    abstract protected function generateField($field, $state);

    /**
     * @param $argumentString
     * @return array
     */
    public static function parseOptions($argumentString)
    {
        $options = array();
        $options['arguments'] = array_map(function ($arg) {
            return trim($arg);
        }, explode(',', $argumentString));
        return $options;
    }

    /**
     * @param $field
     * @param $state
     * @return array
     */
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
            $values[] = $this->generateOne($field, $state);
        } else if ($field->fieldType === Field::FT_HAS_MANY
            || $field->fieldType === Field::FT_MANY_MANY
            || $field->fieldType === Field::FT_ROOT
        ) {
            $values = $this->generateMany($field, $state);
        }

        return $values;
    }

    /**
     * @param $field
     * @param $upState
     * @param int $index
     * @return mixed
     */
    protected function generateObject($field, $upState, $index = 0)
    {
        $className = $field->dataType;
        $object = \QuickDataObject::create($className);

        $state = $upState->down($field, $object, $index);

        foreach ($field->fields as $objectField) {
            if ($objectField->ignore) {
                continue;
            }

            $values = $objectField->provider->generate($objectField, $state);
            if (!empty($values)) {
                $object->setField($objectField->fieldName, $values[0]);
            }
        }

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
                    $object->setField($relation, $value->getField('ID'));
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
                        $id = $object->getField('ID');
                        foreach ($values as $value) {
                            // set has_many field to object ID
                            $value->setField($relation, $id);
                            $writer->write($value, $hasManyField);
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

    /**
     * @param $field
     * @param $state
     * @return mixed
     */
    protected function generateOne($field, $state)
    {
        $object = $this->generateObject($field, $state);
        return $object;
    }

    /**
     * @param $field
     * @param $state
     * @return array
     */
    protected function generateMany($field, $state)
    {
        $objects = array();
        for ($i = 0; $i < $field->count; $i++) {
            $objects[] = $this->generateObject($field, $state, $i);
        }
        return $objects;
    }

    /**
     * @return array
     */
    public function get_shorthand()
    {
        return $this->config()->get('shorthand');
    }

    /**
     * @return array
     */
    public function get_order()
    {
        return $this->config()->get('order');
    }

    /**
     * @param $writer
     */
    public function setWriter($writer)
    {
        $this->writer = $writer;
    }
}
