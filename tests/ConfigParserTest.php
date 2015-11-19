<?php

namespace Seeder\Tests;

use Seeder\Helpers\ConfigParser;
use Seeder\Util\Field;
use Seeder\Util\RecordWriter;

/**
 * Class ConfigParserTest
 * @package Seeder\Tests
 */
class ConfigParserTest extends \SapphireTest
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
    public function testObjectConfig2Field_EmptyPropertiesConfig_CreatesFieldWithObjectFields()
    {
        $config = new ConfigParser(new RecordWriter());

        $field = $config->objectConfig2Field(array(
            'class' => 'Seeder\Tests\Dog',
            'key' => 'Dog',
            'provider' => 'Seeder\Tests\TestProvider',
            'count' => 100,
            'ignore' => array('Breed'),
            'fields' => array(
                'Name' => array(
                    'provider' => 'Seeder\Tests\TestProvider',
                ),
                'Age' => 'test()',
            ),
        ));

        $this->assertEquals('Seeder\Tests\Dog', $field->dataType);
        $this->assertEquals('Dog', $field->key);
        $this->assertInstanceOf('Seeder\Tests\TestProvider', $field->provider);
        $this->assertEquals(100, $field->count);
        $this->assertEquals(100, $field->totalCount);
        $this->assertCount(2, $field->fields);
        $this->assertCount(0, $field->hasOne);
        $this->assertCount(0, $field->hasOne);
        $this->assertCount(0, $field->hasMany);
        $this->assertCount(0, $field->manyMany);

        $nameField = $field->fields[0];
        $this->assertEquals(Field::FT_FIELD, $nameField->fieldType);
        $this->assertStringStartsWith('Varchar', $nameField->dataType);
        $this->assertEquals('Name', $nameField->name);
        $this->assertEquals('Name', $nameField->fieldName);
        $this->assertInstanceOf('Seeder\Tests\TestProvider', $nameField->provider);
        $this->assertEquals(100, $nameField->totalCount);

        $ageField = $field->fields[1];
        $this->assertEquals(Field::FT_FIELD, $ageField->fieldType);
        $this->assertEquals('Int', $ageField->dataType);
        $this->assertEquals('Age', $ageField->name);
        $this->assertEquals('Age', $ageField->fieldName);
        $this->assertInstanceOf('Seeder\Tests\TestProvider', $ageField->provider);
        $this->assertEquals(100, $ageField->totalCount);
    }

    /**
     *
     */
    public function testObjectConfig2Field_HasOneConfig_CreatesFieldWithHasOneField()
    {
        $config = new ConfigParser(new RecordWriter());

        $field = $config->objectConfig2Field(array(
            'class' => 'Seeder\Tests\Human',
            'fields' => array(
                'Parent' => array(
                    'count' => 10,
                    'provider' => 'Seeder\Tests\TestProvider',
                ),
            ),
        ));

        $parentField = $field->hasOne[0];
        $this->assertEquals(Field::FT_HAS_ONE, $parentField->fieldType);
        $this->assertEquals('Parent', $parentField->name);
        $this->assertEquals('ParentID', $parentField->fieldName);
        $this->assertEquals('Parent', $parentField->methodName);
        $this->assertInstanceOf('Seeder\Tests\TestProvider', $parentField->provider);
        $this->assertEquals(1, $parentField->count);
        $this->assertEquals(1, $parentField->totalCount);
        $this->assertCount(2, $parentField->fields);
        $this->assertCount(0, $parentField->hasOne);
    }

    /**
     *
     */
    public function testObjectConfig2Field_HasManyConfig_CreatesFieldWithHasManyField()
    {
        $config = new ConfigParser(new RecordWriter());

        $field = $config->objectConfig2Field(array(
            'class' => 'Seeder\Tests\Dog',
            'fields' => array(
                'Treats' => array(
                    'count' => 10,
                    'provider' => 'Seeder\Tests\TestProvider',
                ),
            ),
        ));

        $treatsField = $field->hasMany[0];
        $this->assertEquals(Field::FT_HAS_MANY, $treatsField->fieldType);
        $this->assertEquals('Treats', $treatsField->name);
        $this->assertEquals('Treats', $treatsField->methodName);
        $this->assertInstanceOf('Seeder\Tests\TestProvider', $treatsField->provider);
        $this->assertEquals(10, $treatsField->count);
        $this->assertEquals(10, $treatsField->totalCount);
        $this->assertCount(2, $treatsField->fields);
    }

    /**
     *
     */
    public function testObjectConfig2Field_ManyManyConfig_CreatesFieldWithManyManyField()
    {
        $config = new ConfigParser(new RecordWriter());

        $field = $config->objectConfig2Field(array(
            'class' => 'Seeder\Tests\Human',
            'fields' => array(
                'Pets' => array(
                    'count' => 5,
                    'provider' => 'Seeder\Tests\TestProvider',
                ),
            ),
        ));

        $petsField = $field->manyMany[0];
        $this->assertEquals(Field::FT_MANY_MANY, $petsField->fieldType);
        $this->assertEquals('Pets', $petsField->name);
        $this->assertEquals('Pets', $petsField->methodName);
        $this->assertInstanceOf('Seeder\Tests\TestProvider', $petsField->provider);
        $this->assertEquals(5, $petsField->count);
        $this->assertEquals(5, $petsField->totalCount);
        $this->assertCount(2, $petsField->fields);
    }

    /**
     *
     */
    public function testObjectConfig2Field_NestedFields_CorrectTotalCount()
    {
        $config = new ConfigParser(new RecordWriter());

        $field = $config->objectConfig2Field(array(
            'class' => 'Seeder\Tests\Human',
            'count' => 100,
            'fields' => array(
                'Pets' => array(
                    'count' => 50,
                ),
            ),
        ));

        $petsField = $field->manyMany[0];
        $this->assertEquals(100, $field->totalCount);
        $this->assertEquals(5000, $petsField->totalCount);
    }

    /**
     *
     */
    public function testObjectConfig2Field_ExplicitAndImplicitFields_MarkedCorrectly()
    {
        $config = new ConfigParser(new RecordWriter());

        $field = $config->objectConfig2Field(array(
            'class' => 'Seeder\Tests\Human',
            'fields' => array(
                'Name' => 'test()',
            ),
        ));

        $nameField = $field->fields[0];
        $this->assertTrue($nameField->explicit);

        $ageField = $field->fields[1];
        $this->assertFalse($ageField->explicit);
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
