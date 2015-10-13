<?php

namespace LittleGiant\SilverStripeSeeder\Tests;


class Treat extends \DataObject implements \TestOnly
{
    public static $db = array(
        'Brand' => 'Varchar',
        'Flavour' => 'Varchar',
    );

    private static $has_one = array(
        'Pet' => 'LittleGiant\SilverStripeSeeder\Tests\Pet',
    );
}

