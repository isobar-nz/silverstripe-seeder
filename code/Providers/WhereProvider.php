<?php

namespace Seeder;

use Exception;

class WhereProvider extends Provider
{
    /**
     * @var string
     */
    public static $shorthand = 'where';

    /**
     * @param mixed $field
     * @param mixed $state
     */
    protected function generateField($field, $state)
    {
        throw new Exception('where provider does not support generating db fields');
    }

    /**
     * @param mixed $field
     * @param mixed $state
     *
     * @return mixed
     */
    protected function generateOne($field, $state)
    {
        $arguments = $field->options['arguments'];

        if (count($arguments) < 3) {
            throw new Exception('too few arguments');
        }

        list($class, $key, $value) = $arguments;

        if (!class_exists($class)) {
            throw new Exception($class . ' does not exist');
        }

        $object = $class::get()->filter($key, $value)->first();

        if (is_null($object)) {
            throw new Exception('object not found');
        }

        return $object;
    }

    /**
     * @param mixed $field
     * @param mixed $state
     *
     * @return mixed
     */
    protected function generateMany($field, $state)
    {
        return $this->generateOne($field, $state);
    }
}
