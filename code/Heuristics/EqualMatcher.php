<?php

namespace LittleGiant\SilverStripeSeeder\Heuristics;

class EqualMatcher implements Matcher
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function match($value)
    {
        return $value == $this->value;
    }
}
