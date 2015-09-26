<?php

use Faker\Factory;
use LittleGiant\SilverStripeSeeder\Providers\Provider;

class FakerProvider extends Provider
{
    private $faker;

    public static $shorthand = 'Faker';

    public function __construct()
    {
        parent::__construct();

        $this->faker = Factory::create();
    }

    public static function parseOptions($argumentString)
    {
        $options = array();
        $arguments = array_map(function ($arg) {
            return trim($arg);
        }, explode(',', $argumentString));

        $options['type'] = array_shift($arguments);
        $options['arguments'] = $arguments;

        return $options;
    }

    protected function generateField($field, $state)
    {
        if (empty($field->arguments['type'])) {
            return null;
        }

        $type = $field->arguments['type'];
        if (!empty($field->arguments['arguments'])) {
            return call_user_func_array(array($this->faker, $type), $field->arguments['arguments']);
        } else {
            return $this->faker->$type;
        }
    }
}
