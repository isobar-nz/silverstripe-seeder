<?php

namespace Seeder\Tests;

use Seeder\Provider;

/**
 * Class TestProvider
 * @package Seeder\Tests
 */
class TestProvider extends Provider
{
    /**
     * @var string
     */
    public static $shorthand = 'test';

    /**
     *
     */
    const TEST_BOOL = false;
    /**
     *
     */
    const TEST_STRING = 'test';
    /**
     *
     */
    const TEST_DATE = '2024-08-17';
    /**
     *
     */
    const TEST_TIME = '12:05:34';
    /**
     *
     */
    const TEST_DATETIME = '2021-10-02 12:59:18';
    /**
     *
     */
    const TEST_DECIMAL = 100.15;
    /**
     *
     */
    const TEST_INT = 176;

    /**
     * @param $field
     * @param $state
     * @return bool|float|int|string
     */
    protected function generateField($field, $state)
    {
        $type = strtolower($field->dataType);
        switch ($type) {
            case 'boolean':
                return self::TEST_BOOL;
            case 'currency':
            case 'decimal':
                return self::TEST_DECIMAL;
            case 'int':
                return self::TEST_INT;
            case 'ss_datetime':
                return self::TEST_DATETIME;
            case 'date':
                return self::TEST_DATE;
            case 'time':
                return self::TEST_TIME;
            default:
                return self::TEST_STRING;
        }
    }
}
