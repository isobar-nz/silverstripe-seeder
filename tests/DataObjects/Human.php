<?php

namespace Seeder\Tests;


/**
 * Class Human
 * @package Seeder\Tests
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
        'Parent' => 'Seeder\Tests\Human',
        'House' => 'Seeder\Tests\House',
    );

    /**
     * @var array
     */
    private static $many_many = array(
        'Pets' => 'Seeder\Tests\Pet',
        'Children' => 'Seeder\Tests\Human',
    );

    /**
     * @var array
     */
    private static $belongs_many_many = array(
        'Parents' => 'Seeder\Tests\Human',
    );

}
