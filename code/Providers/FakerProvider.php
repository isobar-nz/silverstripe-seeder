<?php

use Faker\Factory;
use LittleGiant\SilverStripeSeeder\Providers\Provider;

class FakerProvider extends Provider
{
    private $faker;

    public function __construct()
    {
        parent::__construct();

        $this->faker = Factory::create();
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
