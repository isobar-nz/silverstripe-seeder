<?php

namespace LittleGiant\SilverStripeSeeder\Tests;


/**
 * Class Treat
 * @package LittleGiant\SilverStripeSeeder\Tests
 */
class Treat extends \DataObject implements \TestOnly
{
    /**
     * @var array
     */
    public static $db = array(
        'Brand' => 'Varchar',
        'Flavour' => 'Varchar',
    );

    /**
     * @var array
     */
    private static $has_one = array(
        'Pet' => 'LittleGiant\SilverStripeSeeder\Tests\Pet',
    );
}

