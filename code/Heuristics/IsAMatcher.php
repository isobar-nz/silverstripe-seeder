<?php

namespace LittleGiant\SilverStripeSeeder\Heuristics;

use LittleGiant\SilverStripeSeeder\Util\Field;

class IsAMatcher implements Matcher
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function match($value)
    {
        if ($value instanceof Field) {
            return is_a($value->dataType, $this->value);
        }
        return is_a($value, $this->value);
    }
}
