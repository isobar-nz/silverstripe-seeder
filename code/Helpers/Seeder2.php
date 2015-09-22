<?php

use \LittleGiant\SilverStripeSeeder\Util\Field;
use \LittleGiant\SilverStripeSeeder\Util\SeederState;
use \LittleGiant\SilverStripeSeeder\Providers\DataTypeProvider;
use \LittleGiant\SilverStripeSeeder\Util\RecordWriter;

class Seeder2 extends Object
{
    private $writer;

    public function __construct()
    {
        parent::__construct();
        $this->writer = new RecordWriter();
    }

    public function seed()
    {
        srand();

        $dataObjects = $this->config()->generate;

        if (is_array($dataObjects)) {
            foreach ($dataObjects as $index => $option) {
                if (isset($option['ClassName']) && class_exists($option['ClassName'])) {
                    // add support for count, currently makes 1
                    $field = $this->createObjectField($option['ClassName'], $option);
                    $field->name = $option['ClassName'];
                    $field->fieldType = Field::FT_HAS_ONE; // change to has_many?
                    $field->fieldName = '';
                    $field->methodName = '';

                    $state = new SeederState();
                    $field->provider->generate($field, $state);
                }
            }
        }

        $this->writer->finish();
    }

    public function createObjectField($className, $options)
    {
        $field = new Field();
        $field->dataType = $className;
        $field->arguments = $options;
        $field->provider = $this->createProvider($options);

        $object = singleton($className);

        $ancestry = array();
        foreach ($object->getClassAncestry() as $className) {
            $classObject = singleton($className);
            $ancestry[] = $classObject;
        }

        $field->ancestry = $ancestry;

        $ignoreLookup = array();
        if (!empty($options['ignore']) && is_array($options['ignore'])) {
            foreach ($options['ignore'] as $field) {
                $ignoreLookup[$field] = $field;
            }
        }

        $fields = array();
        $hasOneFields = array();
        $hasManyFields = array();
        $manyManyFields = array();
        foreach ($field->ancestry as $classObject) {
            foreach (\DataObject::custom_database_fields($classObject->ClassName) as $fieldName => $fieldType) {
                if ($fieldType !== 'foreignkey' && !isset($ignoreLookup[$fieldName])) {
                    $fields[$fieldName] = $fieldType;
                }
            }

            // limit to Image fields + fields that specify use
            foreach ($classObject->has_one() as $fieldName => $className) {
                if (!isset($ignoreLookup[$fieldName])) {
                    $hasOneFields[$fieldName] = $className;
                }
            }

            // limit to fields that specify use
            foreach ($classObject->has_many() as $fieldName => $className) {
                if (!isset($ignoreLookup[$fieldName])) {
                    $hasManyFields[$fieldName] = $classObject;
                }
            }

            // limit to fields that specify use
            foreach ($classObject->many_many() as $fieldName => $className) {
                if (!isset($ignoreLookup[$fieldName])) {
                    $manyManyFields[$fieldName] = $classObject;
                }
            }
        }

        $properties = isset($options['properties']) ? $options['properties'] : array();

        foreach ($fields as $fieldName => $dataType) {
            $fieldOptions = isset($properties[$fieldName]) ? $properties[$fieldName] : array();
            $fieldObject = $this->createField($dataType, $fieldOptions);
            $fieldObject->name = $fieldName;
            $field->fields[] = $fieldObject;
        }

        foreach ($hasOneFields as $fieldName => $className) {
            $fieldOptions = isset($properties[$fieldName]) ? $properties[$fieldName] : array();
            $fieldObject = $this->createObjectField($className, $fieldOptions);
            $fieldObject->fieldType = Field::FT_HAS_ONE;
            $fieldObject->name = $fieldName;
            $fieldObject->fieldName = $fieldName . 'ID';
            $fieldObject->methodName = $fieldName;
            $field->hasOne[] = $fieldObject;
        }

        foreach ($hasManyFields as $fieldName => $className) {
            $fieldOptions = isset($properties[$fieldName]) ? $properties[$fieldName] : array();
            $fieldObject = $this->createObjectField($className, $fieldOptions);
            $fieldObject->fieldType = Field::FT_HAS_MANY;
            $fieldObject->name = $fieldName;
            $fieldObject->methodName = $fieldName;
            $field->hasMany[] = $fieldObject;
        }

        foreach ($manyManyFields as $fieldName => $className) {
            $fieldOptions = isset($properties[$fieldName]) ? $properties[$fieldName] : array();
            $fieldObject = $this->createObjectField($className, $fieldOptions);
            $fieldObject->fieldType = Field::FT_MANY_MANY;
            $fieldObject->name = $fieldName;
            $fieldObject->methodName = $fieldName;
            $field->manyMany[] = $fieldObject;
        }

        return $field;
    }

    public function createField($dataType, $options)
    {
        $field = new Field();
        $field->fieldType = Field::FT_FIELD;
        $field->dataType = $dataType;
        $field->arguments = $options;
        $field->provider = $this->createProvider($options);
        return $field;
    }

    public function createProvider($options)
    {
        $provider = new DataTypeProvider();
        $provider->setWriter($this->writer);
        return $provider;
    }
}