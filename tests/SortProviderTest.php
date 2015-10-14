<?php

namespace LittleGiant\SilverStripeSeeder\Tests;

use LittleGiant\SilverStripeSeeder\Helpers\ConfigParser;
use LittleGiant\SilverStripeSeeder\Util\SeederState;
use SiteTree;
use SortProvider;

/**
 * Class SortProviderTest
 * @package LittleGiant\SilverStripeSeeder\Tests
 */
class SortProviderTest extends \SapphireTest
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
                'Sort' => 'sort()',
            ),
        ));

        $sortField = null;
        foreach ($field->fields as $dbField) {
            if ($dbField->name === 'Sort') {
                $sortField = $dbField;
            }
        }

        $this->assertNotNull($sortField);

        $state = new SeederState($field, new SiteTree());
        $fieldState = $state->down($sortField);

        $provider = new SortProvider();

        $value1 = $provider->generate($sortField, $fieldState);
        $value2 = $provider->generate($sortField, $fieldState);
        $value3 = $provider->generate($sortField, $fieldState);

        $this->assertTrue($value1[0] < $value2[0]);
        $this->assertTrue($value2[0] < $value3[0]);
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
