<?php

namespace Seeder\Tests;


/**
 * Class Pet
 * @package Seeder\Tests
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
        'Treats' => 'Seeder\Tests\Treat',
    );

    /**
     * @var array
     */
    private static $belongs_many_many = array(
        'BelongsHuman' => 'Seeder\Tests\Human',
    );
}

