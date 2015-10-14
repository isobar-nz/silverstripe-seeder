<?php

namespace LittleGiant\SilverStripeSeeder;

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
     * @return mixed
     */
    public function creatingDataObject($className);

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
