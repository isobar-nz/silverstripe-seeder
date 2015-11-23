<?php

use LittleGiant\SilverStripeSeeder\Providers\Provider;

/**
 * Class UniqueProvider
 */
class UniqueProvider extends Provider
{
    /**
     * @var string
     */
    public static $shorthand = 'unique';

    /**
     * @param $field
     * @param $state
     * @return string
     */
    protected function generateField($field, $state)
    {
        $prefix = '';
        if (count($field->options['arguments'])) {
            $prefix = $field->options['arguments'][0];
        }

        return uniqid($prefix);
    }
}
