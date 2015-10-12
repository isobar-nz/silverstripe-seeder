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
            'class' => $args[0],
        );

        if (count($options) > 1) {
            $options['count'] = $options[1];
        }

        return $options;
    }

    protected function generateField($field, $state)
    {
        throw new Exception('object provider does not support generating db fields');
    }

    protected function generateHasOneField($field, $state)
    {
        if (empty($field->arguments['class'])) {
            throw new Exception('object provider requires a \'class\'');
        }
        if (!class_exists($field->arguments['class'])) {
            throw new Exception("class '{$field->arguments['class']}' does not exist");
        }

        $className = $field->arguments['class'];
        $object = $className::get()->first();

        if (!$object) {
            SS_Log::log("object for {$field->arguments['class']} not found", SS_Log::WARN);
        }

        return $object;
    }

    protected function generateHasManyField($field, $state)
    {
        if (empty($field->arguments['class'])) {
            throw new Exception('object provider requires a \'class\'');
        }
        if (!class_exists($field->arguments['class'])) {
            throw new Exception("class '{$field->arguments['class']}' does not exist");
        }

        // error checking
        $className = $field->arguments['class'];
        $objects = $className::get()->sort('RAND()')->limit($field->count)->toArray();
        return $objects;
    }

    protected function generateManyManyField($field, $state)
    {
        return $this->generateHasManyField($field, $state);
    }
}
