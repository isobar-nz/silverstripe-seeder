<?php

namespace Seeder\Helpers;
use Seeder\Heuristics\Heuristic;
use Seeder\Heuristics\EqualMatcher;
use Seeder\Heuristics\Condition;
use Seeder\Heuristics\LikeMatcher;
use Seeder\Heuristics\GreaterThanMatcher;
use Seeder\Heuristics\LessThanMatcher;
use Seeder\Heuristics\IsAMatcher;

/**
 * Class HeuristicParser
 * @package Seeder\Heuristics
 */
class HeuristicParser
{
    /**
     * @var ConfigParser
     */
    private $configParser;

    /**
     *
     */
    public function __construct()
    {
        $this->configParser = new ConfigParser();
    }

    /**
     * @param $config
     * @return array
     * @throws \Exception
     */
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

            $heuristic = new Heuristic($name);
            $heuristic->setOptions($options);

            if (array_key_exists('nocache', $heuristicConfig)) {
                $noCache = true;
                if ($heuristicConfig['nocache'] !== null) {
                    $noCache = boolval($heuristicConfig['nocache']);
                }
                $heuristic->setNoCache($noCache);
            }

            if (isset($heuristicConfig['cache'])) {
                $cacheSize = intval($heuristicConfig['cache']);
                $heuristic->setCache($cacheSize);
            }

            if (array_key_exists('ignore', $heuristicConfig)) {
                $ignore = true;
                if ($heuristicConfig['ignore'] !== null) {
                    $ignore = boolval($heuristicConfig['ignore']);
                }
                $heuristic->setIgnore($ignore);
            }

            foreach ($heuristicConfig['conditions'] as $field => $options) {
                $condition = new Condition($field);

                if (!is_array($options)) {
                    $options = array($options);
                }

                foreach ($options as $option) {
                    $matcher = new EqualMatcher($option);

                    preg_match('/^([a-zA-Z0-9_-]+)\((.+)\)$/', $option, $matches);
                    if (count($matches) === 3) {
                        $matcherName = strtolower($matches[1]);
                        if ($matcherName === 'like') {
                            $matcher = new LikeMatcher($matches[2]);
                        } else if ($matcherName === 'gt') {
                            $matcher = new GreaterThanMatcher($matches[2]);
                        } else if ($matcherName === 'lt') {
                            $matcher = new LessThanMatcher($matches[2]);
                        } else if ($matcherName === 'is_a') {
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
