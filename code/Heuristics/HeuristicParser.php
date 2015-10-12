<?php

namespace LittleGiant\SilverStripeSeeder\Heuristics;

use LittleGiant\SilverStripeSeeder\Helpers\ConfigParser;

class HeuristicParser
{
    private $configParser;

    public function __construct()
    {
        $this->configParser = new ConfigParser();
    }

    public function parse($config)
    {
        if (!is_array($config)) {
            return array();
        }

        $heuristics = array();

        foreach ($config as $name => $heuristicConfig) {
            if (empty($heuristicConfig['conditions']) || !is_array($heuristicConfig['conditions'])) {
                continue;
            }

            $options = isset($heuristicConfig['field']) ? $heuristicConfig['field'] : array();
            if (is_string($options)) {
                $options = $this->configParser->parseProviderOptions($options);
            }

            $heuristic = new Heuristic($name, $options);

            foreach ($heuristicConfig['conditions'] as $field => $options) {
                $condition = new Condition($field);

                if (!is_array($options)) {
                    $options = array($options);
                }

                foreach ($options as $option) {
                    $matcher = new EqualMatcher($option);

                    preg_match('/^(\w+)\((.+)\)$/', $option, $matches);
                    if (count($matches) === 3) {
                        if ($matches[1] === 'like') {
                            $matcher = new LikeMatcher($matches[2]);
                        } else if ($matches[1] === 'gt') {
                            $matcher = new GreaterThanMatcher($matches[2]);
                        } else if ($matches[1] === 'lt') {
                            $matcher = new LessThanMatcher($matches[2]);
                        } else if ($matches[1] === 'is_a') {
                            $matcher = new IsAMatcher($matches[2]);
                        }
                    }

                    $condition->addMatcher($matcher);
                }

                $heuristic->addCondition($condition);
            }

            $heuristics[] = $heuristic;
        }

        return $heuristics;
    }
}
