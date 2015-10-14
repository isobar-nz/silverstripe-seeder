<?php

namespace LittleGiant\SilverStripeSeeder\Tests;


/**
 * Class Human
 * @package LittleGiant\SilverStripeSeeder\Tests
 */
class Human extends \DataObject implements \TestOnly
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
    private static $has_one = array(
        'Parent' => 'LittleGiant\SilverStripeSeeder\Tests\Human',
        'House' => 'LittleGiant\SilverStripeSeeder\Tests\House',
    );

    /**
     * @var array
     */
    private static $many_many = array(
        'Pets' => 'LittleGiant\SilverStripeSeeder\Tests\Pet',
        'Children' => 'LittleGiant\SilverStripeSeeder\Tests\Human',
    );

    /**
     * @var array
     */
    private static $belongs_many_many = array(
        'Parents' => 'LittleGiant\SilverStripeSeeder\Tests\Human',
    );
}
