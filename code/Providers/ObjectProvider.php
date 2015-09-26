<?php

use LittleGiant\SilverStripeSeeder\Providers\Provider;

class ObjectProvider extends Provider
{
    public static $shorthand = 'Object';

    public static function parseOptions($argumentString)
    {
        $args = array_map(function ($arg) {
            return trim($arg);
        }, explode(',', $argumentString));

        $options = array(
            'classname' => $args[0],
        );
        if (count($options) > 1) {
            $options['count'] = $options[1];
        }

        return $options;
    }

    protected function generateField($field, $state)
    {
        // error
    }

    protected function generateHasOneField($field, $state)
    {
        // error checking
        $className = $field->arguments['classname'];
        $object = $className::get()->first();
        return $object;
    }

    protected function generateHasManyField($field, $state)
    {
        $count = $field->arguments['count'] ? $field->arguments['count'] : 1;

        // error checking
        $className = $field->arguments['classname'];
        $objects = $className::get()->sort('RAND()')->limit($count)->toArray();
        return $objects;
    }

    protected function generateManyManyField($field, $state)
    {
        return $this->generateHasManyField($field, $state);
    }
}
