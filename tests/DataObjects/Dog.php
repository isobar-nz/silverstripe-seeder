<?php

namespace LittleGiant\SilverStripeSeeder\Tests;


/**
 * Class Dog
 * @package LittleGiant\SilverStripeSeeder\Tests
 */
class Dog extends Pet implements \TestOnly
{
    /**
     * @var array
     */
    public static $db = array(
        'Breed' => 'Varchar',
    );
}
