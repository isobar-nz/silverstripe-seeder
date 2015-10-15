<?php

namespace LittleGiant\SilverStripeSeeder;

/**
 * Interface OutputFormatter
 * @package LittleGiant\SilverStripeSeeder
 */
/**
 * Interface OutputFormatter
 * @package LittleGiant\SilverStripeSeeder
 */
interface OutputFormatter
{
    /**
     * @return mixed
     */
    public function beginSeed();

    /**
     * @param $className
     * @param $key
     * @return mixed
     */
    public function alreadySeeded($className, $key);

    /**
     * @param $className
     * @param $key
     * @return mixed
     */
    public function creatingDataObject($className, $key);

    /**
     * @param $className
     * @param $count
     * @return mixed
     */
    public function dataObjectsCreated($className, $count);

    /**
     * @return mixed
     */
    public function beginUnseed();

    /**
     * @param $deleted
     * @return mixed
     */
    public function reportDataObjectsDeleted($deleted);
}
