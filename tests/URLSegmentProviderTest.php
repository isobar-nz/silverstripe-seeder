<?php

namespace Seeder\Tests;

use Seeder\Helpers\ConfigParser;
use Seeder\Util\SeederState;
use SiteTree;
use Seeder\URLSegmentProvider;

/**
 * Class URLSegmentProviderTest
 * @package Seeder\Tests
 */
class URLSegmentProviderTest extends \SapphireTest
{
    /**
     * @var bool
     */
    protected $usesDatabase = true;

    /**
     *
     */
    public function testGenerate_SiteTreeSort_ReturnsIncreasingSort() {
        $config = new ConfigParser();
        $field = $config->objectConfig2Field(array(
            'class' => 'SiteTree',
            'fields' => array(
                'URLSegment' => 'urlSegment()',
            ),
        ));

        $urlField = null;
        foreach ($field->fields as $dbField) {
            if ($dbField->name === 'URLSegment') {
                $urlField = $dbField;
            }
        }

        $this->assertNotNull($urlField);

        $state = new SeederState($field, new SiteTree());

        $provider = new URLSegmentProvider();

        $url = $provider->generate($urlField, $state);

        $this->assertEquals('site-tree', $url[0]);
    }

//    /**
//     *
//     */
//    public static function tearDownAfterClass()
//    {
//        parent::tearDownAfterClass();
//        \SapphireTest::delete_all_temp_dbs();
//    }
}

