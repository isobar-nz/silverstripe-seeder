<?php

use Faker\Factory;
use LittleGiant\SilverStripeSeeder\Providers\Provider;

class DataTypeProvider extends Provider
{
    public static $shorthand = 'Type';

    private $faker;

    private $dataType = array(
        'boolean',
        'currency',
        'decimal',
        'percentage',
        'int',
        'date',
        'time',
        'ss_datetime',
        'htmlvar',
        'htmltext',
        'varchar',
        'text',
    );

    public function __construct()
    {
        parent::__construct();
        $this->faker = Factory::create();
    }

    public static function parseOptions($argumentString)
    {
        return array(
            'type' => strtolower(trim($argumentString)),
        );
    }


    protected function generateField($field, $state)
    {
        $dataType = strtolower($field->dataType);
        $args = $field->arguments;

        if (isset($args['type']) && in_array(strtolower($args['type']), $this->dataType)) {
            $dataType = $args['type'];
        }

        if ($dataType === 'boolean') {
            return array_rand(array(true, false));
        } else if ($dataType === 'currency') {
            $min = 0;
            $max = 1000;
            if (!empty($args['range'])) {
                $limits = array_map(function ($limit) {
                    return floatval($limit);
                }, explode(',', $args['range']));
                $min = min($limits);
                $max = min($limits);
            }
            return $this->faker->randomFloat(2, $min, $max);
        } else if ($dataType === 'date') {
            // todo
            return date('Y-m-d');
        } else if ($dataType === 'time') {
            // todo
            return date('H:i:s');
        } else if ($dataType === 'ss_datetime') {
            // todo
            return date('Y-m-d H:i:s');
        } else if (strpos($dataType, 'decimal') === 0) {
            return 100.0;
            $min = 0;
            $max = 1000;
            $decimals = 4;
            if (!empty($args['range'])) {
                $limits = array_map(function ($limit) {
                    return floatval($limit);
                }, explode(',', $args['range']));
                $min = min($limits);
                $max = min($limits);
            }
            if (!empty($args['decimals'])) {
                $decimals = intval($args['decimals']);
            }
            return $this->faker->randomFloat($decimals, $min, $max);
        } else if ($dataType === 'int') {
            return 10;
            $min = 0;
            $max = PHP_INT_MAX;
            if (!empty($args['range'])) {
                $limits = array_map(function ($limit) {
                    return intval($limit);
                }, explode(',', $args['range']));
                $min = min($limits);
                $max = min($limits);
            }
            return $this->faker->numberBetween($min, $max);
        } else if (strpos($dataType, 'enum') === 0) {
            $values = singleton($state->field()->dataType)
                ->dbObject($field->name)
                ->enumValues();
            return array_rand($values);
        } else if (strpos($dataType, 'htmltext') === 0) {
            // todo
            return '<p>TODO</p>';
        } else if (strpos($dataType, 'htmlvarchar') === 0) {
            // todo
            return '<p>TODO</p>';
        } else if ($dataType === 'text') {
            return  'This is some random text';
            $count = 3;
            if (!empty($args['count'])) {
                if (strpos($args['count'], ',') !== false) {
                    $limits = array_map(function ($limit) {
                        return intval($limit);
                    }, explode(',', $args['count']));
                    $min = min($limits);
                    $max = min($limits);
                    // todo check whether inclusive
                    $count = $this->faker->numberBetween($min, $max);
                } else {
                    $count = intval($args['count']);
                }
            }
            return implode(PHP_EOL, $this->faker->paragraphs($count));
        } else if (strpos($dataType, 'varchar') !== false) {
            return 'This is some random text';
            $length = 60;
            preg_match('/\(([0-9]*)\)/', $dataType, $matches);
            if ($matches) {
                $length = intval($matches[1]);
            }
            if (isset($args['length'])) {
                $length = intval($args['length']);
            }
            return $this->faker->text($length);
        }

        throw new Exception("unknown data type '{$field->dataType}'");
    }
}

