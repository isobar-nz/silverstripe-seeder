<?php

use LittleGiant\SilverStripeSeeder\Providers\Provider;

class HTMLProvider extends Provider
{
    public static $shorthand = 'html';

    private $faker;

    public function __construct()
    {
        parent::__construct();
        $this->faker = Faker\Factory::create();
    }

    protected function generateField($field, $state)
    {
        $elements = array();

        $elements[] = '<p>' . $this->faker->paragraph(2) . '</p>';
        $elements[] = '<p>' . $this->faker->paragraph(4) . '</p>';

        $list = '<ul>'. PHP_EOL;
        for ($i = 0; $i < rand(1, 5); $i++) {
            $list .= "<li><a href=\"{$this->faker->url}\">{$this->faker->sentence(10)}</a></li>" . PHP_EOL;
        }
        $list .= '</ul>';

        $elements[] = $list;
        
    }
}

