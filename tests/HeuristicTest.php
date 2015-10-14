<?php

namespace LittleGiant\SilverStripeSeeder\Tests;

use LittleGiant\SilverStripeSeeder\Heuristics\HeuristicParser;
use LittleGiant\SilverStripeSeeder\Util\Field;

/**
 * Class HeuristicTest
 * @package LittleGiant\SilverStripeSeeder\Tests
 */
class HeuristicTest extends \SapphireTest
{
    /**
     *
     */
    public function testMatch_IsAMatcher_SiteTreeIsASiteTree()
    {
        $parser = new HeuristicParser();
        $heuristics = $parser->parse(array(
            'URLSegment' => array(
                'conditions' => array(
                    'name' => 'URLSegment',
                    'parent' => 'is_a(SiteTree)',
                ),
                'field' => 'URLSegment()',
            )
        ));

        $heuristic = $heuristics[0];

        $field = new Field();
        $field->name = 'Page';
        $field->dataType = 'SiteTree';

        $urlField = new Field();
        $urlField->name = 'URLSegment';
        $urlField->dataType = 'Varchar';
        $urlField->fieldType = Field::FT_FIELD;
        $urlField->parent = $field;

        $field->fields[] = $urlField;

        $this->assertTrue($heuristic->match($urlField));

        $heuristic->apply($urlField);

        $this->assertInstanceOf('URLSegmentProvider', $urlField->provider);
    }
}
