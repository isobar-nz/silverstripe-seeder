<?php

namespace Seeder\Tests;


/**
 * Class House
 * @package Seeder\Tests
 */
class House extends \DataObject implements \TestOnly
{
    /**
     * @var array
     */
    public static $db = array(
        'Address' => 'Varchar(255)',
    );

    /**
     * @var array
     */
    private static $many_many = array(
        'Occupants' => 'Seeder\Tests\Human',
    );
}

