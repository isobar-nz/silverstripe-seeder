<?php

namespace LittleGiant\SilverStripeSeeder\Providers;

use LittleGiant\SilverStripeSeeder\Util\Field;

abstract class Provider extends \Object
{
    protected $writer;

    abstract protected function generateField($field, $state);

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
        } else if ($field->fieldType === Field::FT_HAS_MANY) {
            $values = $this->generateHasManyField($field, $state);
        } else if ($field->fieldType === Field::FT_MANY_MANY) {
            $values = $this->generateManyManyField($field, $state);
        }

        return $values;
    }

    protected function generateObject($field, $upState, $index = 0)
    {
        $className = $field->dataType;
        $object = new $className();

        $state = $upState->down($field, $object, $index);

        foreach ($field->fields as $objectField) {
            $values = $objectField->provider->generate($objectField, $state);
            if (!empty($values)) {
                $fieldName = $objectField->fieldName;
                $object->$fieldName = $values[0];
            }
        }

        $this->writer->write($object, $field);

        foreach ($field->hasOne as $hasOneField) {
            $hasOneField->arguments['count'] = 1;
            $values = $hasOneField->provider->generate($hasOneField, $state);
            if (!empty($values[0])) {
                $fieldName = $hasOneField->fieldName;
                $object->$fieldName = $values[0]->ID;
            }
        }

        foreach ($field->manyMany as $manyManyField) {
            $values = $manyManyField->provider->generate($manyManyField, $state);
            if (!empty($values)) {
                $methodName = $manyManyField->methodName;
                $object->$methodName()->addMany($values);
            }
        }

        $this->writer->write($object, $field);

        foreach ($field->hasMany as $hasManyField) {
            $values = $hasManyField->provider->generate($hasManyField, $state);
            if (!empty($values)) {
                $linkField = '';
                foreach ($values[0]->has_one() as $fieldName => $className) {
                    if ($className === $object->ClassName) {
                        $linkField = $fieldName . 'ID';
                    }
                }
                if ($linkField) {
                    foreach ($values as $value) {
                        $value->$linkField = $object->ID;
                        $this->writer->write($value, $hasManyField);
                    }
                }
            }
        }

        return $object;
    }

    protected function generateHasOneField($field, $state)
    {
        // can we get rid of use,
        // and replace with a existingObjectProvider, randomObjectProvider

        // add use support
        $object = $this->generateObject($field, $state);
        return $object;
    }

    protected function generateHasManyField($field, $state)
    {
        // add use support
        $count = 1;
        if (isset($field->arguments['count'])) {
            $count = intval($field->arguments['count']);
        }

        $objects = array();
        for ($i = 0; $i < $count; $i++) {
            $objects[] = $this->generateObject($field, $state, $i);
        }
        return $objects;
    }

    protected function generateManyManyField($field, $state)
    {
        // add use support
        $count = 1;
        if (isset($field->arguments['count'])) {
            $count = intval($field->arguments['count']);
        }

        $objects = array();
        for ($i = 0; $i < $count; $i++) {
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
