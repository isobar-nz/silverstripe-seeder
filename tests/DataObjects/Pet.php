<?php

namespace LittleGiant\SilverStripeSeeder\Tests;


/**
 * Class Pet
 * @package LittleGiant\SilverStripeSeeder\Tests
 */
class Pet extends \DataObject implements \TestOnly
{
    /**
     * @var array
     */
    public static $db = array(
        'Name' => 'Varchar(60)',
        'Age' => 'Int',
    );

    /**
     * @var array
     */
    private static $has_many = array(
        'Treats' => 'LittleGiant\SilverStripeSeeder\Tests\Treat',
    );

    /**
     * @var array
     */
    private static $belongs_many_many = array(
        'BelongsHuman' => 'LittleGiant\SilverStripeSeeder\Tests\Human',
    );
}

