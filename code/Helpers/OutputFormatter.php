<?php

namespace LittleGiant\SilverStripeSeeder;

/**
 * Interface OutputFormatter
 */
/**
 * Interface OutputFormatter
 * @package LittleGiant\SilverStripeSeeder
 */
interface OutputFormatter
{
    /**
     * @param $className
     * @return mixed
     */
    public function classDoesNotHaveExtension($className);

    /**
     * @param $className
     * @param $count
     * @param $currentCount
     * @return mixed
     */
    public function fakingClassRecords($className, $count, $currentCount = 0);

    /**
     * @param $className
     * @param $parentClassName
     * @return mixed
     */
    public function parentClassDoesNotExist($className, $parentClassName);

    /**
     * @param $className
     * @param $hasOneField
     * @param $hasOneClassName
     * @return mixed
     */
    public function noInstancesOfHasOneClass($className, $hasOneField, $hasOneClassName);

    /**
     * @param $dataType
     * @return mixed
     */
    public function unknownDataType($dataType);

    /**
     * @param $className
     * @param $count
     * @return mixed
     */
    public function deletingClassRecords($className, $count);

    /**
     * @return mixed
     */
    public function flush();
}
