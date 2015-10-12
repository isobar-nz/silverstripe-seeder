<?php

namespace LittleGiant\SilverStripeSeeder\Heuristics;

class LikeMatcher implements Matcher
{
    private $like;

    private $matchStart = true;
    private $matchEnd = true;

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

    public function match($value)
    {
        $value = strtolower($value);
        if ($this->matchStart && $this->matchEnd) {
            return $this->like == $value;
        } else if ($this->matchStart) {
            return strpos($value, $this->like) === 0;
        } else if ($this->matchEnd) {
            return strpos(strrev($value), strrev($this->like)) === 0;
        }
        return strpos($value, $this->like);
    }
}
