<?php

namespace Seeder\Heuristics;

/**
 * Interface Matcher
 * @package Seeder\Heuristics
 */
interface Matcher
{
    /**
     * @param $value
     * @return mixed
     */
    public function match($value);
}
