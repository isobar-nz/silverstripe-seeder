<?php

namespace Seeder\Heuristics;

/**
 * Class GreaterThanMatcher
 * @package Seeder\Heuristics
 */
class GreaterThanMatcher implements Matcher
{
    /**
     * @var
     */
    private $value;

    /**
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @param $value
     * @return bool
     */
    public function match($value)
    {
        return $value > $this->value;
    }
}
