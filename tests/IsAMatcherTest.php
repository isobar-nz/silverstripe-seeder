<?php

namespace LittleGiant\SilverStripeSeeder\Tests;

use LittleGiant\SilverStripeSeeder\Heuristics\IsAMatcher;

/**
 * Class IsAMatcherTest
 * @package LittleGiant\SilverStripeSeeder\Tests
 */
class IsAMatcherTest extends \SapphireTest
{
    /**
     *
     */
    public function testMatch_MatchClasses_SubClassesMatch()
    {
        $matcher = new IsAMatcher('DataObject');

        $this->assertFalse($matcher->match('Object'));
        $this->assertTrue($matcher->match('DataObject'));
        $this->assertTrue($matcher->match('SiteTree'));
    }
}
