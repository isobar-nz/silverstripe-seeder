<?php

namespace LittleGiant\SilverStripeSeeder;

/**
 * Class HtmlOutputFormatter
 * @package LittleGiant\SilverStripeSeeder
 */
class HtmlOutputFormatter implements OutputFormatter
{
    /**
     * @var array
     */
    private $recordsGenerated = array();

    /**
     * @var array
     */
    private $recordsDeleted = array();

    /**
     * @var array
     */
    private $errors = array();


    /**
     * @param $className
     * @return mixed
     */
    public function classDoesNotHaveExtension($className)
    {
        $this->errors[] = "'{$className}' does not have the 'SeederExtension'";
    }

    /**
     * @param $className
     * @param $count
     * @param $currentCount
     * @return mixed
     */
    public function fakingClassRecords($className, $count, $currentCount = 0)
    {
        if (!array_key_exists($className, $this->recordsGenerated)) {
            $this->recordsGenerated[$className] = array('generated' => 0, 'alreadyExisting' => 0);
        }
        $this->recordsGenerated[$className]['generated'] += max($count, 0);
        $this->recordsGenerated[$className]['alreadyExisting'] += $currentCount;
    }

    /**
     * @param $className
     * @param $parentClassName
     * @return mixed
     */
    public function parentClassDoesNotExist($className, $parentClassName)
    {
        $this->errors[] = "Cannot set parent for {$className}, no {$parentClassName} exist";
    }

    /**
     * @param $className
     * @param $hasOneField
     * @param $hasOneClassName
     * @return mixed
     */
    public function noInstancesOfHasOneClass($className, $hasOneField, $hasOneClassName)
    {
        $this->errors[] = "Cannot create {$className} has_one {$hasOneField}, no {$hasOneClassName} exist";
    }

    /**
     * @param $dataType
     * @return mixed
     */
    public function unknownDataType($dataType)
    {
        $this->errors[] = 'Unknown data type "' . $dataType . '"';
    }

    /**
     * @param $className
     * @param $count
     * @return mixed
     */
    public function deletingClassRecords($className, $count)
    {
        if (!array_key_exists($className, $this->recordsDeleted)) {
            $this->recordsDeleted[$className] = 0;
        }
        $this->recordsDeleted[$className] += $count;
    }

    /**
     * @return mixed
     */
    public function flush()
    {
        $renderer = new \DebugView();
        $renderer->writeHeader();

        echo '<div class="info"><h2>Seeder</h2></div>';

        $totalGenerated = 0;
        foreach ($this->recordsGenerated as $counts) {
            $totalGenerated += $counts['generated'];
        }

        if ($totalGenerated) {
            echo '<h3>Records generated</h3>';
            foreach ($this->recordsGenerated as $className => $counts) {
                if ($counts['alreadyExisting']) {
                    echo "<li>{$counts['generated']} '{$className}' faked ({$counts['alreadyExisting']} already exist)</li>";
                } else {
                    echo "<li>{$counts['generated']} '{$className}' faked</li>";
                }
            }
        } else {
            echo '<h4>No records generated</h4>';
            foreach ($this->recordsGenerated as $className => $counts) {
                if ($counts['alreadyExisting']) {
                    echo "<li>{$counts['alreadyExisting']} '{$className}' records already exist</li>";
                }
            }
        }

        $totalDeleted = 0;
        foreach ($this->recordsDeleted as $count) {
            $totalDeleted += $count;
        }

        if ($totalDeleted) {
            echo '<h3>Records deleted</h3>';
            foreach ($this->recordsDeleted as $className => $count) {
                echo "<li>{$count} '{$className}' seeds deleted</li>";
            }
        } else {
            echo '<h4>No records deleted</h4>';
        }


        if ($this->errors) {
            $count = count($this->errors);
            echo "<h3>{$count} errors occurred</h3>";
            foreach ($this->errors as $error) {
                echo '<p>' . $error . '</p>';
            }
        }

        $renderer->writeFooter();
    }
}
