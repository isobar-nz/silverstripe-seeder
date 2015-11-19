<?php

namespace LittleGiant\SilverStripeSeeder\Heuristics;

/**
 * Class LikeMatcher
 * @package LittleGiant\SilverStripeSeeder\Heuristics
 */
class LikeMatcher implements Matcher
{
    /**
     * @var string
     */
    private $like;

    /**
     * @var bool
     */
    private $matchStart = true;
    /**
     * @var bool
     */
    private $matchEnd = true;

    /**
     * @param $like
     */
    public function __construct($like)
    {
        if (strpos($like, '%') === 0) {
            $this->matchStart = false;
            $like = substr($like, 1);
        }

        if (strpos(strrev($like), '%') === 0) {
            $this->matchEnd = false;
            $like = substr($like, 0, strlen($like) - 1);
        }

        $this->like = strtolower($like);
    }

    /**
     * @param $value
     * @return bool
     */
    public function match($value)
    {
        if (!is_string($value)) {
            return false;
        }

        $value = strtolower($value);
        if ($this->matchStart && $this->matchEnd) {
            return $this->like == $value;
        } else if ($this->matchStart) {
            return strpos($value, $this->like) === 0;
        } else if ($this->matchEnd) {
            return strpos(strrev($value), strrev($this->like)) === 0;
        }
        return strpos($value, $this->like) !== false;
    }
}
