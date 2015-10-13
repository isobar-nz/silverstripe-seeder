<?php

use LittleGiant\SilverStripeSeeder\Providers\Provider;

class UniqueProvider extends Provider
{
    public static $shorthand = 'unique';

    protected function generateField($field, $state)
    {
        $prefix = '';
        if (count($field->arguments['arguments'])) {
            $prefix = $field->arguments['arguments'][0];
        }

        return uniqid($prefix);
    }
}
