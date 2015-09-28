<?php

namespace LittleGiant\SilverStripeSeeder;

use LittleGiant\SilverStripeSeeder\Util\CounterTree;

class CliOutputFormatter implements OutputFormatter
{
    public function beginSeed()
    {
        echo PHP_EOL;
        echo 'seeding database...', PHP_EOL;
        echo PHP_EOL;
    }

    public function creatingDataObject($className)
    {
        echo "creating '{$className}'...", PHP_EOL;
    }

    public function dataObjectsCreated($className, $count)
    {
        echo "{$count} '{$className}' created", PHP_EOL;
    }

    public function reportDataObjectsCreated(CounterTree $tree)
    {
        echo PHP_EOL;
        $nodes = $tree->getTree();
        foreach ($nodes as $node) {
            $this->printTree($node);
        }
        echo PHP_EOL;
    }

    private function printTree(&$node, $depth = 0)
    {
        $details = "- {$node['class']} ({$node['count']})";
        echo str_repeat('  ', $depth) . $details, PHP_EOL;;

        foreach ($node['children'] as $child) {
            $this->printTree($child, $depth + 1);
        }
    }

    public function beginUnseed()
    {
        echo PHP_EOL;
        echo 'unseeding database...', PHP_EOL;
        echo PHP_EOL;
    }

    public function reportDataObjectsDeleted($deleted)
    {
        foreach ($deleted as $className => $count) {
            echo "deleted {$count} '{$className}'", PHP_EOL;
        }
        echo PHP_EOL;
    }

}
