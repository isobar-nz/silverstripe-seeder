<?php

namespace Seeder;

/**
 * Class DateProvider
 * @package Seeder
 */
class DateProvider extends Provider
{
    /**
     * @var string
     */
    public static $shorthand = 'Date';

    /**
     * @param $field
     * @param $state
     * @return bool|string
     */
    protected function generateField($field, $state)
    {
        $time = 'now';

        $format = 'Y-m-d H:i:s';
        $type = strtolower($field->dataType);
        if ($type === 'date') {
            $format = 'Y-m-d';
        } elseif ($type === 'time') {
            $format = 'H:i:s';
        }

        if (!empty($field->options['arguments'])) {
            $args = $field->options['arguments'];
            $time = $args[0];

            if (count($args) >= 2) {
                $format = $args[1];
            }
        }

        return date($format, strtotime($time));
    }
}
