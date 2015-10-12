<?php

use LittleGiant\SilverStripeSeeder\Helpers\ConfigParser;
use LittleGiant\SilverStripeSeeder\Heuristics\HeuristicParser;
use \LittleGiant\SilverStripeSeeder\OutputFormatter;
use \LittleGiant\SilverStripeSeeder\Util\Field;
use \LittleGiant\SilverStripeSeeder\Util\SeederState;
use \LittleGiant\SilverStripeSeeder\Util\RecordWriter;

class Seeder extends Object
{
    private $writer;

    private $ignoreSeeds = false;

    private $outputFormatter;

    public function __construct($writer, OutputFormatter $outputFormatter)
    {
        parent::__construct();
        $this->writer = $writer;
        $this->outputFormatter = $outputFormatter;
    }

    public function seed($className = null, $key = null)
    {
        // seed random to get different results each run
        srand();

        $this->outputFormatter->beginSeed();

        $dataObjects = $this->config()->create;

        $configParser = new ConfigParser($this->writer);

        $heuristics = array();
        if ($this->config()->heuristics) {
            $heuristicParser = new HeuristicParser();
            $heuristics = $heuristicParser->parse($this->config()->heuristics);
        }

        if (is_array($dataObjects)) {
            foreach ($dataObjects as $index => $option) {
                if (is_string($index) && class_exists($index)) {
                    $option['class'] = $index;
                }

                if (empty($option['key'])) {
                    $option['key'] = $option['class'];
                };

                if (class_exists($option['class'])
                    && (!$className || $className === $option['class'])
                    && (!$key || $key === $option['key'])
                ) {
                    $this->outputFormatter->creatingDataObject($option['class']);
                    $field = $configParser->objectConfig2Field($option);
                    $field->name = $option['class'];
                    // has_many will generate the number passed in count
                    $field->fieldName = '';
                    $field->methodName = '';
                    $field->count = $this->getCount($field);

                    $this->applyHeuristics($field, $heuristics);

                    $state = new SeederState();
                    $objects = $field->provider->generate($field, $state);

                    $this->writer->finish();

                    $this->outputFormatter->dataObjectsCreated($option['class'], count($objects));
                }
            }
        } else {
            throw new Exception('\'create\' must be an array');
        }
    }

    private function getCount($field)
    {
        $count = isset($field->arguments['count']) ? $field->arguments['count'] : 1;

        if ($this->ignoreSeeds) {
            return $count;
        }

        $currentCount = SeedRecord::get()->filter(array(
            'Root' => true,
            'SeedClassName' => $field->dataType,
            'Key' => $field->key,
        ))->Count();
        $count -= $currentCount;

        return $count;
    }

    public function applyHeuristics($field, $heuristics)
    {
        if (!$field->explicit) {
            $matching = array();
            foreach ($heuristics as $heuristic) {
                if ($heuristic->match($field)) {
                    $matching[] = $heuristic;
                }
            }

            usort($matching, function ($h1, $h2) {
                return $h1->getSpecificity() - $h2->getSpecificity();
            });

            foreach ($matching as $heuristic) {
                $heuristic->apply($field);
            }
        }

        foreach ($field->fields as $db) {
            $this->applyHeuristics($db, $heuristics);
        }

        foreach ($field->hasOne as $hasOneField) {
            $this->applyHeuristics($hasOneField, $heuristics);
        }

        foreach ($field->hasMany as $hasManyField) {
            $this->applyHeuristics($hasManyField, $heuristics);
        }

        foreach ($field->manyMany as $manyManyField) {
            $this->applyHeuristics($manyManyField, $heuristics);
        }
    }

    public function unseed($key = null)
    {
        $this->outputFormatter->beginUnseed();

        // TODO check which classes (check super classes as well, above SiteTree/DataObjet) have onBeforeDelete/onAfterDelete and feed through ->delete()
        // TODO delete has_many tables

        echo 'Seed count: ', SeedRecord::get()->Count(), PHP_EOL;

        $deleted = array();

        while (SeedRecord::get()->Count()) {
            $seeds = SeedRecord::get()->limit(1000);
            if ($key) {
                $seeds = $seeds->filter('Key', $key);
            }
            // sort by id desc to delete in reverse
            $seeds = $seeds->sort('ID DESC');


            $classes = array();
            foreach ($seeds as $seed) {
                $className = $seed->SeedClassName;
                $classes[$className][] = $seed->SeedID;
                $this->writer->delete($seed);
            }

            foreach ($classes as $className => $ids) {
                $versioned = DataObject::has_extension($className, 'Versioned');
                if ($versioned) {
                    $this->writer->deleteIDsFromStage($className, $ids, 'Stage', 'Live');
                } else {
                    $this->writer->deleteIDs($className, $ids);
                }

                $deleted[$className] = count($ids);
            }

            $this->writer->finish();
        }

        $this->outputFormatter->reportDataObjectsDeleted($deleted);
    }

    public function setIgnoreSeeds($ignoreSeeds = false)
    {
        $this->ignoreSeeds = $ignoreSeeds;
    }
}
