<?php

namespace Seeder;

use Faker\Factory;

/**
 * Class DataTypeProvider
 * @package Seeder
 */
class DataTypeProvider extends Provider
{
    /**
     * @var string
     */
    public static $shorthand = 'Type';

    /**
     * @var
     */
    private $faker;

    /**
     * @var array
     */
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

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->faker = Factory::create();
    }

    /**
     * @param $argumentString
     * @return array
     */
    public static function parseOptions($argumentString)
    {
        return array(
            'type' => strtolower(trim($argumentString)),
        );
    }


    /**
     * @param $field
     * @param $state
     * @return bool|mixed|null|string
     */
    protected function generateField($field, $state)
    {
        $dataType = strtolower($field->dataType);
        $args = isset($field->options['arguments']) ? $field->options['arguments'] : array();

        if (isset($args['type']) && in_array(strtolower($args['type']), $this->dataType)) {
            $dataType = $args['type'];
        }

        if (strpos($dataType, 'boolean') === 0) {
            return array_rand(array(true, false));
        } else if (strpos($dataType, 'currency') === 0) {
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
        } else if (strpos($dataType, 'date') === 0) {
            return date('Y-m-d');
        } else if (strpos($dataType, 'time') === 0) {
            return date('H:i:s');
        } else if (strpos($dataType, 'ss_datetime') === 0) {
            return date('Y-m-d H:i:s');
        } elseif (strpos($dataType, 'decimal') === 0) {
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
        } else if (strpos($dataType, 'int') === 0) {
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
        } elseif (strpos($dataType, 'enum') === 0) {
            $values = singleton($state->field()->dataType)
                ->dbObject($field->name)
                ->enumValues();
            return array_rand($values);
        } elseif (strpos($dataType, 'htmltext') === 0) {
            return "<p>{$this->faker->paragraph(5)}</p>";
        } elseif (strpos($dataType, 'htmlvarchar') === 0) {
            return "<p>{$this->faker->sentence(10)}</p>";
        } else if (strpos($dataType, 'text') === 0) {
            $count = 3;
            if (!empty($args['count'])) {
                if (strpos($args['count'], ',') !== false) {
                    $limits = array_map(function ($limit) {
                        return intval($limit);
                    }, explode(',', $args['count']));
                    $min = min($limits);
                    $max = min($limits);
                    $count = $this->faker->numberBetween($min, $max);
                } else {
                    $count = intval($args['count']);
                }
            }
            return implode(PHP_EOL, $this->faker->paragraphs($count));
        } elseif (strpos($dataType, 'varchar') !== false) {
            $length = 60;
            preg_match('/\(([0-9]*)\)/', $dataType, $matches);
            if ($matches) {
                $length = intval($matches[1]);
            }
            if (isset($args['length'])) {
                $length = intval($args['length']);
            }
            // faker requires length >= 5
            return $this->faker->text(max($length, 5));
        }

        return null;
    }
}
