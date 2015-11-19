<?php

namespace Seeder;

/**
 * Class UniqueProvider
 * @package Seeder
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
        if (count($field->arguments['arguments'])) {
            $prefix = $field->arguments['arguments'][0];
        }

        return uniqid($prefix);
    }
}
