<?php

use \LittleGiant\SilverStripeSeeder\Util\Field;
use \LittleGiant\SilverStripeSeeder\Util\SeederState;
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

        $dataObjects = $this->config()->create;

        if (is_array($dataObjects)) {
            foreach ($dataObjects as $index => $option) {
                if (is_string($index) && class_exists($index)) {
                    $option['classname'] = $index;
                }

                if (isset($option['classname']) && class_exists($option['classname'])) {
                    // add support for count, currently makes 1
                    $field = $this->createObjectField($option['classname'], $option);
                    $field->name = $option['classname'];
                    // has many generates count
                    $field->fieldType = Field::FT_HAS_MANY;
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

        if (!is_array($options)) {
            $options = $this->parseProviderOptions($options);
        }

        $field->arguments = $options;

        $object = singleton($className);

        $ancestry = array();
        foreach ($object->getClassAncestry() as $className) {
            $classObject = singleton($className);
            $ancestry[] = $classObject;
        }

        $field->ancestry = $ancestry;

        $ignoreFields = $this->getIgnoreFields($field, $options);

        $ignoreLookup = array();
        foreach ($ignoreFields as $ignoreField) {
            $ignoreLookup[$ignoreField] = $ignoreField;
        }

        $properties = isset($options['properties']) ? $options['properties'] : array();

        $fields = array();
        $hasOneFields = array();
        $hasManyFields = array();
        $manyManyFields = array();
        foreach ($field->ancestry as $classObject) {
            foreach (\DataObject::custom_database_fields($classObject->ClassName) as $fieldName => $fieldType) {
                $ignored = isset($ignoreLookup[$fieldName]) && !isset($properties[$fieldName]);
                if ($fieldType !== 'ForeignKey' && !$ignored) {
                    $fields[$fieldName] = $fieldType;
                }
            }

            foreach ($classObject->has_one() as $fieldName => $className) {
                $ignored = isset($ignoreLookup[$fieldName]) && !isset($properties[$fieldName]);
                if (!$ignored
                    && isset($options['properties'])
                    && array_key_exists($fieldName, $options['properties'])
                ) {
                    $hasOneFields[$fieldName] = $className;
                }
            }

            // limit to fields that specify use
            foreach ($classObject->has_many() as $fieldName => $className) {
                $ignored = isset($ignoreLookup[$fieldName]) && !isset($properties[$fieldName]);
                if (!$ignored
                    && isset($options['properties'])
                    && array_key_exists($fieldName, $options['properties'])
                ) {
                    $hasManyFields[$fieldName] = $className;
                }
            }

            // limit to fields that specify use
            foreach ($classObject->many_many() as $fieldName => $className) {
                $ignored = isset($ignoreLookup[$fieldName]) && !isset($properties[$fieldName]);
                if (!$ignored
                    && isset($options['properties'])
                    && array_key_exists($fieldName, $options['properties'])
                ) {
                    $manyManyFields[$fieldName] = $className;
                }
            }
        }

        $properties = array_merge($this->getDefaultProperties($field), $properties);

        foreach ($fields as $fieldName => $dataType) {
            $fieldOptions = isset($properties[$fieldName]) ? $properties[$fieldName] : array();
            $fieldObject = $this->createField($dataType, $fieldOptions);
            $fieldObject->fieldName = $fieldName;
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

        $field->provider = $this->createProvider($field, $options);

        return $field;
    }

    public function parseProviderOptions($optionString)
    {
        if (preg_match('/([^(]+)\(([^)]+)?\)/', $optionString, $matches)) {
            $shorthand = strtolower($matches[1]);
            $arguments = isset($matches[2]) ? $matches[2] : '';

            foreach ($this->config()->providers as $provider) {
                if (isset($provider::$shorthand)) {
                    if (strtolower($provider::$shorthand) === $shorthand) {
                        $options = $provider::parseOptions($arguments);
                        $options['provider'] = $provider;
                        return $options;
                    }
                }
            }
            // maybe throw error?
        }

        $provider = $this->config()->empty_shorthand_provider;
        $options = $provider::parseOptions($optionString);
        $options['provider'] = $provider;
        return $options;
    }

    public function getIgnoreFields($field, $options)
    {
        $defaultIgnores = $this->config()->default_ignores;

        $ignoreFields = array();
        foreach ($field->ancestry as $object) {
            if (isset($defaultIgnores[$object->ClassName])) {
                $ignoreFields = array_merge($ignoreFields, $defaultIgnores[$object->ClassName]);
            }
        }

        if (isset($options['ignore']) && is_array($options['ignore'])) {
            $ignoreFields = array_merge($ignoreFields, $options['ignore']);
        }

        return array_unique($ignoreFields);
    }

    public function getDefaultProperties($field)
    {
        $properties = array();

        $defaultValues = $this->config()->default_values;
        foreach ($field->ancestry as $object) {
            if (isset($defaultValues[$object->ClassName])) {
                $properties = array_merge($properties, $defaultValues[$object->ClassName]);
            }
        }

        return $properties;
    }

    public function createField($dataType, $options)
    {
        $field = new Field();
        $field->fieldType = Field::FT_FIELD;
        $field->dataType = $dataType;

        if (!is_array($options)) {
            $options = $this->parseProviderOptions($options);
        }

        $field->arguments = $options;
        $field->provider = $this->createProvider($field, $options);
        return $field;
    }

    public function createProvider($field, $options)
    {
        if (!empty($options['provider'])) {
            $providerClassName = $options['provider'];
            $provider = new $providerClassName();
        } else {
            $provider = $this->getDefaultProvider($field);
        }

        $provider->setWriter($this->writer);
        return $provider;
    }

    public function getDefaultProvider($field)
    {
        $providerClassName = $this->config()->default_provider;

        $defaultProviders = $this->config()->default_providers;
        foreach ($field->ancestry as $object) {
            if (isset($defaultProviders[$object->ClassName])) {
                $providerClassName = $defaultProviders[$object->ClassName];
            }
        }

        // check data type since this will let db fields be overwritten
        if (isset($defaultProviders[$field->dataType])) {
            $providerClassName = $defaultProviders[$field->dataType];
        }

        $provider = new $providerClassName();
        return $provider;
    }

    public function unseed()
    {
        foreach (Seed::get() as $seed) {
            $className = $seed->SeedClassName;
            $object = $className::get()->byID($seed->SeedID);

            if ($object) {
//                // is this necessary??
//                foreach ($object->many_many() as $method => $type) {
//                    $object->$method()->removeAll();
//                }
//                // is this necessary??
//                foreach ($object->has_many() as $method => $type) {
//                    $object->$method()->removeAll();
//                }

                if ($object->has_extension('Versioned')) {
                    $object->deleteFromStage('Live');
                    $object->deleteFromStage('Stage');
                } else {
                    $object->delete();
                }
            }

            $seed->delete();
        }
    }
}
