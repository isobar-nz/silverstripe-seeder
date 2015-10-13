<?php

namespace LittleGiant\SilverStripeSeeder\Heuristics;

class Condition
{
    private $property;

    private $matchers = array();

    public function __construct($property)
    {
        $this->property = $property;
    }

    public function addMatcher(Matcher $matcher)
    {
        $this->matchers[] = $matcher;
    }

    public function match($field)
    {
        $properties = explode('.', $this->property);

        $value = $field;
        foreach ($properties as $property) {
            if (!is_object($value)) {
                return false;
            }
            $value = $value->$property;
        }

        $isMatch = false;
        foreach ($this->matchers as $matcher) {
            $isMatch = $isMatch || $matcher->match($value);
        }

        return $isMatch;
    }
}
