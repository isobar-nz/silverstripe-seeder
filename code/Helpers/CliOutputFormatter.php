<?php

namespace Seeder\Helpers;

/**
 * Class CliOutputFormatter
 * @package Seeder\Helpers
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
     * @param $key
     * @return mixed
     */
    public function alreadySeeded($className, $key)
    {
        if ($className !== $key) {
            echo "'{$className}' ({$key}) already seeded", PHP_EOL;
        } else {
            echo "'{$className}' already seeded", PHP_EOL;
        }
    }

    /**
     * @param $className
     * @param $key
     * @returns null
     */
    public function creatingDataObject($className, $key)
    {
        if ($className !== $key) {
            echo "creating '{$className}' ({$key})...", PHP_EOL;
        } else {
            echo "creating '{$className}'...", PHP_EOL;
        }
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
