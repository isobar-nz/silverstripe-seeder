<?php

namespace LittleGiant\SilverStripeSeeder\Tests;


/**
 * Class House
 * @package LittleGiant\SilverStripeSeeder\Tests
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
        'Occupants' => 'LittleGiant\SilverStripeSeeder\Tests\Human',
    );
}

