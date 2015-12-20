<?php

namespace Seeder\Tests;

/**
 * Class Dog
 * @package Seeder\Tests
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
