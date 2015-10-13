<?php

namespace LittleGiant\SilverStripeSeeder\Tests;


class House extends \DataObject implements \TestOnly
{
    public static $db = array(
        'Address' => 'Varchar(255)',
    );

    private static $many_many = array(
        'Occupants' => 'LittleGiant\SilverStripeSeeder\Tests\Human',
    );
}

