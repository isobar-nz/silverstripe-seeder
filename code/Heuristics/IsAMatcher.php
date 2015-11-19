<?php

namespace Seeder\Heuristics;

use Seeder\Util\Field;

/**
 * Class IsAMatcher
 * @package Seeder\Heuristics
 */
class IsAMatcher implements Matcher
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
        if ($value instanceof Field) {
            return is_a($value->dataType, $this->value, true);
        }
        return is_a($value, $this->value, true);
    }
}
