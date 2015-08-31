<?php

namespace LittleGiant\SilverStripeSeeder;

/**
 * Class CliOutputFormatter
 * @package LittleGiant\SilverstripeSeeder
 */
class CliOutputFormatter implements OutputFormatter
{

    /**
     * @param $className
     * @return mixed
     */
    public function classDoesNotHaveExtension($className)
    {
        error_log("'{$className}' does not have the 'SeederExtension'");
    }

    /**
     * @param $className
     * @param $count
     * @param $currentCount
     * @return mixed
     */
    public function fakingClassRecords($className, $count, $currentCount = 0)
    {
        if ($currentCount) {
            echo "Faking {$count} '{$className}' ({$currentCount} already exist)", PHP_EOL;
        } else if ($count) {
            echo "Faking {$count} '{$className}'", PHP_EOL;
        } else {
            echo "No records to fake for {$className}";
        }
    }

    /**
     * @param $className
     * @param $parentClassName
     * @return mixed
     */
    public function parentClassDoesNotExist($className, $parentClassName)
    {
        error_log("Cannot set parent for {$className}, no {$parentClassName} exist");
    }

    /**
     * @param $className
     * @param $hasOneField
     * @param $hasOneClassName
     * @return mixed
     */
    public function noInstancesOfHasOneClass($className, $hasOneField, $hasOneClassName)
    {
        error_log("Cannot create {$className} has_one {$hasOneField}, no {$hasOneClassName} exist");
    }

    /**
     * @param $dataType
     * @return mixed
     */
    public function unknownDataType($dataType)
    {
        error_log('Unknown data type "' . $dataType . '"');
    }

    /**
     * @param $className
     * @param $count
     * @return mixed
     */
    public function deletingClassRecords($className, $count)
    {
        if ($count) {
            echo "Cleaning up {$count} seeds for '{$className}'", PHP_EOL;
        } else {
            echo "No records to clean up for {$className}'", PHP_EOL;
        }
    }

    /**
     * @return mixed
     */
    public function flush()
    {
        // empty
    }
}
