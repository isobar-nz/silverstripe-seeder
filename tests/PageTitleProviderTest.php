<?php

namespace LittleGiant\SilverStripeSeeder\Tests;

use LittleGiant\SilverStripeSeeder\Helpers\ConfigParser;
use LittleGiant\SilverStripeSeeder\Util\SeederState;
use SiteTree;
use PageTitleProvider;

/**
 * Class PageTitleProviderTest
 * @package LittleGiant\SilverStripeSeeder\Tests
 */
class PageTitleProviderTest extends \SapphireTest
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
                'Title' => 'pageTitle()',
            ),
        ));

        $titleField = null;
        foreach ($field->fields as $dbField) {
            if ($dbField->name === 'Title') {
                $titleField = $dbField;
            }
        }

        $this->assertNotNull($titleField);

        $state = new SeederState($field, new SiteTree());

        $provider = new PageTitleProvider();

        $title = $provider->generate($titleField, $state);

        $this->assertEquals('Site Tree', $title[0]);
    }

    /**
     *
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        \SapphireTest::delete_all_temp_dbs();
    }
}
