<?php

namespace Seeder\Heuristics;

/**
 * Class Condition
 * @package Seeder\Heuristics
 */
class Condition
{
    /**
     * @var
     */
    private $property;

    /**
     * @var array
     */
    private $matchers = array();

    /**
     * @param $property
     */
    public function __construct($property)
    {
        $this->property = $property;
    }

    /**
     * @param Matcher $matcher
     */
    public function addMatcher(Matcher $matcher)
    {
        $this->matchers[] = $matcher;
    }

    /**
     * @param $field
     * @return bool
     */
    public function match($field)
    {
        $properties = explode('.', $this->property);

        $value = $field;
        foreach ($properties as $property) {
            if (!is_object($value) || !isset($value->$property)) {
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
