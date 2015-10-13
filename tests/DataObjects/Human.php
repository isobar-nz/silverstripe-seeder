<?php

namespace LittleGiant\SilverStripeSeeder\Tests;


class Human extends \DataObject implements \TestOnly
{
    public static $db = array(
        'Name' => 'Varchar(60)',
        'Age' => 'Int',
    );

    private static $has_one = array(
        'Parent' => 'LittleGiant\SilverStripeSeeder\Tests\Human',
        'House' => 'LittleGiant\SilverStripeSeeder\Tests\House',
    );

    private static $many_many = array(
        'Pets' => 'LittleGiant\SilverStripeSeeder\Tests\Pet',
        'Children' => 'LittleGiant\SilverStripeSeeder\Tests\Human',
    );

    private static $belongs_many_many = array(
        'Parents' => 'LittleGiant\SilverStripeSeeder\Tests\Human',
    );
}
