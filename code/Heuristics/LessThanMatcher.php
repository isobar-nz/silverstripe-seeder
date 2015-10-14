<?php

namespace LittleGiant\SilverStripeSeeder\Heuristics;

/**
 * Class LessThanMatcher
 * @package LittleGiant\SilverStripeSeeder\Heuristics
 */
class LessThanMatcher implements Matcher
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
        return $value < $this->value;
    }
}
