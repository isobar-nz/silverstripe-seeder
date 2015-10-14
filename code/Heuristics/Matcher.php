<?php

namespace LittleGiant\SilverStripeSeeder\Heuristics;

/**
 * Interface Matcher
 * @package LittleGiant\SilverStripeSeeder\Heuristics
 */
interface Matcher
{
    /**
     * @param $value
     * @return mixed
     */
    public function match($value);
}
