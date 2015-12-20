<?php

namespace Seeder\Tests;

use Seeder\Helpers\HeuristicParser;
use Seeder\Util\Field;

/**
 * Class HeuristicParserTest
 * @package Seeder\Tests
 */
class HeuristicParserTest extends \SapphireTest
{
    /**
     * @var bool
     */
    protected $usesDatabase = true;

    /**
     * @var array
     */
    protected $extraDataObjects = array(
        'Seeder\Tests\Dog',
        'Seeder\Tests\House',
        'Seeder\Tests\Human',
        'Seeder\Tests\Pet',
        'Seeder\Tests\Treat',
    );

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setUpOnce();
    }

    /**
     *
     */
    public function testParse_HeuristicWithOneCondition_HeuristicParsedCorrectly()
    {
        $parser = new HeuristicParser();

        $heuristics = $parser->parse(array(
            'Dog' => array(
                'conditions' => array(
                    'name' => 'Age'
                ),
            ),
        ));

        $heuristic = $heuristics[0];
        $this->assertEquals(1, $heuristic->getSpecificity());

        $field = new Field();

        $field->name = 'Age';
        $this->assertTrue($heuristic->match($field));

        $field->name = 'Bob';
        $this->assertFalse($heuristic->match($field));
    }

    /**
     *
     */
    public function testParse_HeuristicWithMultipleConditionsMultipleMatchers_HeuristicParsedCorrectly()
    {
        $parser = new HeuristicParser();

        $heuristics = $parser->parse(array(
            'Dog' => array(
                'conditions' => array(
                    'name' => array('Name', 'Age'),
                    'fieldType' => Field::FT_HAS_ONE,
                ),
            ),
        ));

        $heuristic = $heuristics[0];

        $field = new Field();
        $field->name = 'Name';
        $field->fieldType = Field::FT_HAS_ONE;

        $this->assertTrue($heuristic->match($field));

        $field->name = 'Age';
        $this->assertTrue($heuristic->match($field));

        $field->fieldType = Field::FT_HAS_MANY;
        $this->assertFalse($heuristic->match($field));
    }

    /**
     *
     */
    public function testParse_HeuristicsWithLikeMatcher_HeuristicParsedCorrectly()
    {
        $parser = new HeuristicParser();

        $heuristics = $parser->parse(array(
            'Dog' => array(
                'conditions' => array(
                    'name' => array(
                        'like(bob)',
                        'like(%label)',
                        'like(%content%)',
                        'like(home%)',
                    ),
                ),
            ),
        ));

        $heuristic = $heuristics[0];

        $field = new Field();

        $field->name = 'Bob';
        $this->assertTrue($heuristic->match($field));

        $field->name = 'jjbobhh';
        $this->assertFalse($heuristic->match($field));

        $field->name = 'GraphLabel';
        $this->assertTrue($heuristic->match($field));

        $field->name = 'LabelGraph';
        $this->assertFalse($heuristic->match($field));

        $field->name = 'Content';
        $this->assertTrue($heuristic->match($field));

        $field->name = 'HomePage';
        $this->assertTrue($heuristic->match($field));

        $field->name = 'PageHome';
        $this->assertFalse($heuristic->match($field));
    }

    /**
     *
     */
    public function testParse_ParentHeuristics_HeuristicParsedCorrectly()
    {
        $parser = new HeuristicParser();

        $heuristics = $parser->parse(array(
            'Test' => array(
                'conditions' => array(
                    'parent.fieldType' => array(
                        'has_many',
                        'many_many'
                    ),
                ),
            ),
        ));

        $heuristic = $heuristics[0];

        $field = new Field();
        $field->fieldType = 'has_many';

        $childField = new Field();
        $childField->parent = $field;
        $childField->fieldType = 'has_many';

        $this->assertTrue($heuristic->match($childField));

        $field->fieldType = 'has_one';
        $this->assertFalse($heuristic->match($childField));
    }
//
//    /**
//     *
//     */
//    public static function tearDownAfterClass()
//    {
//        parent::tearDownAfterClass();
//        \SapphireTest::delete_all_temp_dbs();
//    }
}
