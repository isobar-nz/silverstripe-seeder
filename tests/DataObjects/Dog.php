<?php

namespace LittleGiant\SilverStripeSeeder\Tests;


class Dog extends Pet implements \TestOnly
{
    public static $db = array(
        'Breed' => 'Varchar',
    );
}
