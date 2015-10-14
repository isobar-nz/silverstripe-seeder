<?php

namespace LittleGiant\SilverStripeSeeder;

use LittleGiant\SilverStripeSeeder\Util\CounterTree;

/**
 * Class CliOutputFormatter
 * @package LittleGiant\SilverStripeSeeder
 */
class CliOutputFormatter implements OutputFormatter
{
    /**
     *
     */
    public function beginSeed()
    {
        echo PHP_EOL;
        echo 'seeding database...', PHP_EOL;
        echo PHP_EOL;
    }

    /**
     * @param $className
     * @returns null
     */
    public function creatingDataObject($className)
    {
        echo "creating '{$className}'...", PHP_EOL;
    }

    /**
     * @param $className
     * @param $count
     * @returns null
     */
    public function dataObjectsCreated($className, $count)
    {
        echo "{$count} '{$className}' created", PHP_EOL;
    }

    /**
     * @param $node
     * @param int $depth
     */
    private function printTree(&$node, $depth = 0)
    {
        $details = "- {$node['class']} ({$node['count']})";
        echo str_repeat('  ', $depth) . $details, PHP_EOL;;

        foreach ($node['children'] as $child) {
            $this->printTree($child, $depth + 1);
        }
    }

    /**
     *
     */
    public function beginUnseed()
    {
        echo PHP_EOL;
        echo 'unseeding database...', PHP_EOL;
        echo PHP_EOL;
    }

    /**
     * @param $deleted
     * @returns null
     */
    public function reportDataObjectsDeleted($deleted)
    {
        foreach ($deleted as $className => $count) {
            echo "deleted {$count} '{$className}'", PHP_EOL;
        }
        echo PHP_EOL;
    }

}
