<?php

use LittleGiant\SilverStripeSeeder\Providers\Provider;

/**
 * Class DateProvider
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
        } else if ($type === 'time') {
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
