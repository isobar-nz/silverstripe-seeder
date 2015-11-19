<?php

namespace Seeder\Heuristics;

/**
 * Class EqualMatcher
 * @package Seeder\Heuristics
 */
class EqualMatcher implements Matcher
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
        return $value == $this->value;
    }
}
