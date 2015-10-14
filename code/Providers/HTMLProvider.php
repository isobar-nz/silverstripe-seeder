<?php

use LittleGiant\SilverStripeSeeder\Providers\Provider;

/**
 * Class HTMLProvider
 */
class HTMLProvider extends Provider
{
    /**
     * @var string
     */
    public static $shorthand = 'html';

    /**
     * @var
     */
    private $faker;

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->faker = Faker\Factory::create();
    }

    /**
     * @param $field
     * @param $state
     * @return string
     */
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

        if (stripos($field->dataType, 'htmltext') !== false) {
            $html = implode($elements, PHP_EOL);
            return $html;
        }

        $key = array_rand($elements);
        return $elements[$key];
    }
}

