<?php

namespace LittleGiant\SilverStripeSeeder\Providers;

use Faker\Factory;
use LittleGiant\SilverStripeSeeder\Util\Field;

class DataTypeProvider extends Provider
{
    private $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function generate($field, $state)
    {
        $values = array();
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

    private function generateField($field, $state)
    {
        $fieldType = strtolower($field->fieldType);
        $args = $field->arguments;

        if ($fieldType === 'boolean') {
            return array_rand(array(true, false));
        } else if ($fieldType === 'currency') {
            $min = 0;
            $max = 1000;
            if (!empty($args['range'])) {
                $limits = array_map(function ($limit) {
                    return floatval($limit);
                }, explode(',', $args['range']));
                $min = min($limits);
                $max = min($limits);
            }
            return $this->faker->randomFloat(2, $min, $max);
        } else if ($fieldType === 'date') {
            // todo
            return date('Y-m-d');
        } else if ($fieldType === 'time') {
            // todo
            return date('H:i:s');
        } else if ($fieldType === 'ss_datetime') {
            // todo
            return date('Y-m-d H:i:s');
        } else if (strpos($fieldType, 'decimal') === 0) {
            $min = 0;
            $max = 1000;
            $decimals = 4;
            if (!empty($args['range'])) {
                $limits = array_map(function ($limit) {
                    return floatval($limit);
                }, explode(',', $args['range']));
                $min = min($limits);
                $max = min($limits);
            }
            if (!empty($args['decimals'])) {
                $decimals = intval($args['decimals']);
            }
            return $this->faker->randomFloat($decimals, $min, $max);
        } else if ($fieldType === 'int') {
            $min = 0;
            $max = PHP_INT_MAX;
            if (!empty($args['range'])) {
                $limits = array_map(function ($limit) {
                    return intval($limit);
                }, explode(',', $args['range']));
                $min = min($limits);
                $max = min($limits);
            }
            return $this->faker->numberBetween($min, $max);
        } else if (strpos($fieldType, 'enum') === 0) {
            $values = singleton($state->up()->field()->dataType)
                ->dbObject($field)
                ->enumValues();
            return array_rand($values);
        } else if (strpos($fieldType, 'htmltext') === 0) {
            // todo
            return '<p>TODO</p>';
        } else if (strpos($fieldType, 'htmlvarchar') === 0) {
            // todo
            return '<p>TODO</p>';
        } else if ($fieldType === 'text') {
            $count = 3;
            if (!empty($args['count'])) {
                if (strpos($args['count'], ',') !== false) {
                    $limits = array_map(function($limit) {
                        return intval($limit);
                    }, explode(',', $args['count']));
                    $min = min($limits);
                    $max = min($limits);
                    // todo check whether inclusive
                    $count = $this->faker->numberBetween($min, $max);
                } else {
                    $count = intval($args['count']);
                }
            }
            return $this->faker->paragraphs($count);
        } else if (strpos($fieldType, 'varchar') !== false) {
            $length = 60;
            preg_match('/\(([0-9]*)\)/', $fieldType, $matches);
            if ($matches) {
                $length = intval($matches[1]);
            }
            if (isset($args['count'])) {
                $length = intval($args['count']);
            }
            return $this->faker->text($length);
        }

        // error message, unknown data type
        return null;
    }

    private function generateHasOneField($field, $state)
    {
        // can we get rid of use,
        // and replace with a existingObjectProvider, randomObjectProvider

        // add use support
        $object = $this->generateObject($field, $state);
        return array($object);
    }

    private function generateHasManyField($field, $state)
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

    private function generateManyManyField($field, $state)
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

    private function generateObject($field, $state, $index = 0)
    {
        $className = $field->dataType();
        $object = new $className();
        // write here to get ID?
        // need ID for nested objects to reference Up

        $newState = $state->down($field, $object, $index);

        foreach ($object->fields as $objectField) {
            $values = $objectField->provider->generate($objectField, $newState);
            if (!empty($values)) {
                $fieldName = $objectField->fieldName;
                $object->$fieldName = $values[0];
            }
        }

        foreach ($object->hasOneFields as $hasOneField) {
            $hasOneField->arguments['count'] = 1;
            $values = $hasOneField->provider->generate($hasOneField, $newState);
            if (!empty($values)) {
                $fieldName = $hasOneField->fieldName;
                $object->$fieldName = $values[0]->ID;
            }
        }

        foreach ($object->manyManyFields as $manyManyField) {
            $values = $manyManyField->provider->generate($manyManyField, $newState);
            if (!empty($values)) {
                $methodName = $manyManyField->methodName;
                $object->$methodName()->addMany($values);
            }
        }

        $this->writer->write($object);

        foreach ($object->hasManyFields as $hasManyField) {
            $values = $hasManyField->provider->generate($hasManyField);
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
                        $this->writer->write($value);
                    }
                }
            }
        }

        return $object;
    }
}