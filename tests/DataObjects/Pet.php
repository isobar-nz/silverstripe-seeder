<?php

namespace LittleGiant\SilverStripeSeeder\Tests;


class Pet extends \DataObject implements \TestOnly
{
    public static $db = array(
        'Name' => 'Varchar(60)',
        'Age' => 'Int',
    );

    private static $has_many = array(
        'Treats' => 'LittleGiant\SilverStripeSeeder\Tests\Treat',
    );

    private static $belongs_many_many = array(
        'BelongsHuman' => 'LittleGiant\SilverStripeSeeder\Tests\Human',
    );
}
