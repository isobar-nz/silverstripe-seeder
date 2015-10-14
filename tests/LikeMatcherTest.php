<?php

namespace LittleGiant\SilverStripeSeeder\Tests;

use LittleGiant\SilverStripeSeeder\Heuristics\LikeMatcher;

/**
 * Class LikeMatcherTest
 * @package LittleGiant\SilverStripeSeeder\Tests
 */
class LikeMatcherTest extends \SapphireTest
{
    /**
     *
     */
    public function testMatch_NoWildCards_MatchesEqualStrings()
    {
        $matcher = new LikeMatcher('hello');

        $this->assertTrue($matcher->match('Hello'));
        $this->assertTrue($matcher->match('HELLO'));
        $this->assertFalse($matcher->match('phello'));
        $this->assertFalse($matcher->match('hellop'));
        $this->assertFalse($matcher->match('zhellop'));
    }

    /**
     *
     */
    public function testMatch_StartHasWildCard_MatchesEndsWith()
    {
        $matcher = new LikeMatcher('%hello');

        $this->assertTrue($matcher->match('Hello'));
        $this->assertTrue($matcher->match('HELLO'));
        $this->assertTrue($matcher->match('phello'));
        $this->assertFalse($matcher->match('hellop'));
        $this->assertFalse($matcher->match('zhellop'));
    }

    /**
     *
     */
    public function testMatch_EndHasWildCard_MatchesBeginsWith()
    {
        $matcher = new LikeMatcher('hello%');

        $this->assertTrue($matcher->match('Hello'));
        $this->assertTrue($matcher->match('HELLO'));
        $this->assertFalse($matcher->match('phello'));
        $this->assertTrue($matcher->match('hellop'));
        $this->assertFalse($matcher->match('zhellop'));
    }

    /**
     *
     */
    public function testMatch_BothEndsHaveWildCards_MatchesContains()
    {
        $matcher = new LikeMatcher('%hello%');

        $this->assertTrue($matcher->match('Hello'));
        $this->assertTrue($matcher->match('HELLO'));
        $this->assertTrue($matcher->match('phello'));
        $this->assertTrue($matcher->match('hellop'));
        $this->assertTrue($matcher->match('zhellop'));
        $this->assertFalse($matcher->match('ello'));
    }
}
