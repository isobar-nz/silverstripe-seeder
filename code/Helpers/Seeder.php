<?php

use \LittleGiant\SilverStripeSeeder\OutputFormatter;
use \LittleGiant\SilverStripeSeeder\CliOutputFormatter;

/**
 * Class Seeder
 */
class Seeder extends Object
{
    /**
     * @var bool
     */
    private $ignoreCurrentRecords = false;

    /**
     * @var
     */
    private $outputFormatter;

    /**
     * @var
     */
    private $faker;

    /**
     * @var array
     */
    private $fieldTypes = array(
        'firstname' => array('firstname'),
        'lastname' => array('lastname', 'surname'),
        'email' => array('email', 'emailaddress'),
        'phone' => array('phone', 'mobile', 'phonenumber'),
        'company' => array('company'),
        'address' => array('address'),
        'address1' => array('address1', 'street'),
        'address2' => array('address2', 'addressline2', 'suburb'),
        'city' => array('city'),
        'postcode' => array('postcode', 'zipcode', 'postalcode'),
        'state' => array('state'),
        'country' => array('country'),
        'countrycode' => array('countrycode'),
        'lat' => array('lat', 'latitude'),
        'lng' => array('lng', 'longitude'),
        'link' => array('link', 'url'),
        'facebooklink' => array('facebook', 'facebooklink'),
        'googlepluslink' => array('googleplus', 'googlepluslink'),
        'twitterlink' => array('twitter', 'twitterlink'),
        'linkedinlink' => array('linkedin', 'linkedinlink'),
        'sort' => array('sort', 'sortorder'),
    );

    /**
     * @var array
     */
    private $dataTypes = array(
        'varchar',
        'text',
        'htmlvar',
        'htmltext',
        'int',
        'percentage',
        'ss_datetime',
        'time',
        'date',
        'decimal',
        'currency',
        'boolean',
    );

    /**
     * @var array
     */
    private $useOptions = array('existing', 'new');

    /**
     * @param OutputFormatter $outputFormatter
     */
    public function __construct(OutputFormatter $outputFormatter = null)
    {
        if (!$outputFormatter) {
            $outputFormatter = new CliOutputFormatter();
        }
        $this->outputFormatter = $outputFormatter;
    }

    /**
     *
     */
    public function seed()
    {
        // seed random number to get different nullables
        srand();

        $this->faker = Faker\Factory::create();

        $dataObjects = $this->config()->DataObjects;

        if (is_array($dataObjects)) {
            foreach ($dataObjects as $className => $data) {
                if (class_exists($className)) {
                    $this->fakeClass($className, $data, $this->ignoreCurrentRecords);
                }
            }
        }

        $this->outputFormatter->flush();
    }

    /**
     * @param $className
     * @param $data
     * @param bool $ignoreCurrentRecords
     * @return array|void
     * @throws ValidationException
     * @throws null
     */
    private function fakeClass($className, $data, $ignoreCurrentRecords = false)
    {
        $fields = $this->getDBFields($className);

        if (!Object::has_extension($className, 'SeederExtension')) {
            $this->outputFormatter->classDoesNotHaveExtension($className);
            return;
        }

        $count = 10;
        if (!empty($data['count'])) {
            $count = $data['count'];
        }

        $currentRecordCount = $className::get()->filter('IsSeed', true)->Count();

        if (!$ignoreCurrentRecords && $currentRecordCount) {
            $count -= $currentRecordCount;
            $count = max($count, 0);
            $this->outputFormatter->fakingClassRecords($className, $count, $currentRecordCount);
        } else {
            $this->outputFormatter->fakingClassRecords($className, $count);
        }

        $createdObjects = array();

        for ($i = 0; $i < $count; $i++) {
            $obj = new $className();

            foreach ($fields as $field => $type) {
                $type = strtolower($type);

                // ignore has one relationships for the moment
                if (isset($data['ignore']) && in_array($field, $data['ignore'])) {
                    continue;
                }

                if ($type === 'foreignkey') {
                    $hasOneField = substr($field, 0, strlen($field) - 2);
                    $type = $obj->has_one($hasOneField);
                    $options = isset($data['properties'][$hasOneField]) ? $data['properties'][$hasOneField] : array();

                    if (is_array($options)) {

                        // value to be generated
                        if (!empty($options['nullable']) && $this->randomNull()) {
                            $obj->$field = null;
                        } else if ($type === 'Image' || is_subclass_of($type, 'Image')) {
                            $this->outputFormatter->fakingClassRecords('Image', 1);
                            $image = $this->createImage($options);
                            if ($image && $image->exists()) {
                                $obj->$field = $image->ID;
                            }
                        } else if ($obj instanceof SiteTree && $hasOneField === 'Parent') {
                            if (!empty($data['parent']) && class_exists($data['parent'])) {
                                $parentClass = $data['parent'];
                                $parentObject = $parentClass::get()->first();
                                if ($parentObject) {
                                    $obj->ParentID = $parentObject->ID;
                                } else {
                                    $this->outputFormatter->parentClassDoesNotExist($className, $parentClass);
                                }
                            }
                        } else if (isset($options['use']) && in_array($options['use'], $this->useOptions)) {
                            if ($options['use'] === 'existing') {
                                $hasOneObject = $type::get()->sort('RAND()')->first();
                                if ($hasOneObject) {
                                    $obj->$field = $hasOneObject->ID;
                                } else {
                                    $this->outputFormatter->noInstancesOfHasOneClass($className, $hasOneField, $type);
                                }
                            } else if ($options['use'] === 'new') {
                                $hasOneObjects = $this->fakeClass($type, $options, true);
                                if ($hasOneObjects) {
                                    $obj->$field = $hasOneObjects[0]->ID;
                                }
                            }
                        }
                    } else {
                        // value given
                        $obj->$field = $options;
                    }
                } else {
                    $options = isset($data['properties'][$field]) ? $data['properties'][$field] : array();
                    if (is_array($options)) {
                        // value to be generated
                        $obj->$field = $this->getSeedValue($className, $field, $type, $options);
                    } else {
                        // value given
                        $obj->$field = $options;
                    }
                }
            }

            foreach ($obj->many_many() as $manyManyField => $type) {
                $options = isset($data['properties'][$manyManyField]) ? $data['properties'][$manyManyField] : array();

                if (isset($options['use']) && in_array($options['use'], $this->useOptions)) {
                    $manyManyCount = $this->calculateCount($options, 'count', 2);
                    $options['count'] = $manyManyCount;

                    $items = ArrayList::create();
                    if ($options['use'] === 'existing') {
                        $items = $type::get()->sort('RAND()')->limit($manyManyCount);
                    } else if ($options['use'] === 'new') {
                        if ($type === 'Image' || is_subclass_of($type, 'Image')) {
                            $this->outputFormatter->fakingClassRecords('Image', $manyManyCount);
                            $items = $this->createImages($options, $manyManyCount);
                        } else {
                            $items = $this->fakeClass($type, $options, true);
                        }
                    }

                    $obj->$manyManyField()->addMany($items);
                }
            }


            $this->writeObject($obj);

            foreach ($obj->has_many() as $hasManyField => $type) {
                $options = isset($data['properties'][$hasManyField]) ? $data['properties'][$hasManyField] : array();

                if (isset($options['use']) && in_array($options['use'], $this->useOptions)) {
                    $hasManyCount = $this->calculateCount($options, 'count', 2);
                    $options['count'] = $hasManyCount;

                    $items = ArrayList::create();
                    if ($options['use'] === 'existing') {
                        $items = $type::get()->sort('RAND()')->limit($hasManyCount);
                    } else if ($options['use'] === 'new') {
                        if ($type === 'Image' || is_subclass_of($type, 'Image')) {
                            $this->outputFormatter->fakingClassRecords('Image', $hasManyCount);
                            $items = $this->createImages($options, $hasManyCount);
                        } else {
                            $items = $this->fakeClass($type, $options, true);
                        }
                    }

                    if (isset($items[0])) {
                        $itemField = '';
                        foreach ($items[0]->has_one() as $hasOneField => $hasOneType) {
                            if ($obj->ClassName === $hasOneType) {
                                $itemField = $hasOneField . 'ID';
                            }
                        }

                        if ($itemField) {
                            foreach ($items as $item) {
                                $item->$itemField = $obj->ID;
                                $this->writeObject($item);
                            }
                        }
                    }
                }
            }

            $createdObjects[] = $obj;
        }

        return $createdObjects;
    }

    /**
     * @param $className
     * @return array
     */
    public function getDBFields($className)
    {
        $fields = array();
        $classes = singleton($className)->getClassAncestry();
        foreach ($classes as $class) {
            $fields = array_merge($fields, DataObject::custom_database_fields($class));
        }
        return $fields;
    }

    /**
     * @param $className
     * @param $field
     * @param $type
     * @param $options
     * @return bool|null|string
     */
    public function getSeedValue($className, $field, $type, $options)
    {
        $fieldLower = strtolower($field);
        $type = strtolower($type);

        foreach ($this->fieldTypes as $fieldType => $fieldNames) {
            if (in_array($fieldLower, $fieldNames)) {
                $type = strtolower($fieldType);
            }
        }

        if (!empty($options['type'])) {
            $overrideType = strtolower($options['type']);
            if (isset($this->fieldTypes[$overrideType]) || in_array($overrideType, $this->dataTypes)) {
                $type = $overrideType;
            }
        }

        $length = $this->calculateCount($options, 'length', false);

        if ($fieldLower === 'isseed') {
            return true;
        } else if (!empty($options['nullable']) && $this->randomNull()) {
            return null;
        } else if (!empty($options['faker_type'])) {
            // TODO add support for faker methods
            $fakerType = $options['faker_type'];
            return $this->faker->$fakerType;
        } else if ($type === 'firstname') {
            return $this->faker->firstName();
        } else if ($type === 'lastname') {
            return $this->faker->lastName;
        } else if ($type === 'email') {
            return $this->faker->safeEmail;
        } else if ($type === 'phone') {
            return $this->faker->phoneNumber;
        } else if ($type === 'company') {
            return $this->faker->company;
        } else if ($type === 'address') {
            return $this->faker->address;
        } else if ($type === 'address1') {
            return $this->faker->streetAddress;
        } else if ($type === 'address2') {
            return $this->faker->secondaryAddress;
        } else if ($type === 'city') {
            return $this->faker->city;
        } else if ($type === 'postcode') {
            return $this->faker->postcode;
        } else if ($type === 'state') {
            return $this->faker->state;
        } else if ($type === 'country') {
            return $this->faker->country;
        } else if ($type === 'countrycode') {
            return $this->faker->countryCode;
        } else if ($type === 'lat') {
            return $this->faker->latitude;
        } else if ($type === 'lng') {
            return $this->faker->longitude;
        } else if ($type === 'link') {
            return $this->faker->url;
        } else if ($type === 'facebooklink') {
            return 'http://facebook.com/';
        } else if ($type === 'googlepluslink') {
            return 'http://plus.google.com/';
        } else if ($type === 'twitterlink') {
            return 'http://twitter.com/';
        } else if ($type === 'linkedinlink') {
            return 'http://linkedin.com/';
        } else if ($type === 'sort') {
            return 0;
        } else if ($type === 'boolean') {
            return array_rand(array(true, false));
        } else if ($type === 'currency') {
            return $this->faker->randomFloat(2, 0, 1000000);
        } else if ($type === 'date') {
            return $this->faker->date();
        } else if ($type === 'decimal') {
            return $this->faker->randomFloat();
        } else if (strpos($type, 'enum') === 0) {
            $values = singleton($className)->dbObject($field)->enumValues();
            return array_rand($values);
        } else if (strpos($type, 'htmltext') === 0) {
            $length = $length === false ? 3 : $length;
            return '<p>' . $this->faker->paragraph($length) . '</p>';
        } else if ($type === 'htmlvarchar') {
            $maxLength = 60;
            preg_match('/\(([0-9]*)\)/', $type, $matches);
            if ($matches) {
                $maxLength = $matches[1];
            }
            // subtract tag chars from length
            $maxLength = max(0, $maxLength - 7);
            if ($length !== false) {
                $maxLength = $length;
            }
            return '<p>' . $this->faker->text($maxLength) . '</p>';
        } else if ($type === 'int') {
            return $this->faker->randomNumber();
        } else if ($type == 'percentage') {
            return $this->faker->randomFloat(4, 0, 1);
        } else if (strpos($type, 'decimal') !== false) {
            return $this->faker->randomFloat(4, 0);
        } else if ($type === 'ss_datetime') {
            return $this->faker->dateTime()->format('Y-m-d H:i:s');
        } else if ($type === 'text') {
            $length = $length === false ? 3 : $length;
            return join(PHP_EOL, $this->faker->paragraphs($length));
        } else if ($type === 'time') {
            return $this->faker->time();
        } else if (strpos($type, 'varchar') !== false) {
            $maxLength = 60;
            preg_match('/\(([0-9]*)\)/', $type, $matches);
            if ($matches) {
                $maxLength = $matches[1];
            }
            if ($length !== false) {
                $maxLength = $length;
            }
            return $this->faker->text($maxLength);
        }

        $this->outputFormatter->unknownDataType($type);
        return '';
    }

    /**
     * @param $options
     * @param int $count
     * @return array
     */
    public function createImages($options, $count = 1)
    {
        $images = array();
        for ($i = 0; $i < $count; $i++) {
            $images[] = $this->createImage($options);
        }
        return $images;
    }

    /**
     * @param $options
     * @return Image
     * @throws ValidationException
     * @throws null
     */
    public function createImage($options)
    {
        $path = BASE_PATH . '/assets/Seed';

        if (!file_exists($path)) {
            mkdir($path);
            chmod($path, 0777);
        }

        $width = $this->calculateCount($options, 'width', 640);
        $height = $this->calculateCount($options, 'height', 480);

        $fileName = $this->faker->image($path, $width, $height);
        $fileName = str_replace(BASE_PATH . '/', '', $fileName);

        $image = new Image();
        $image->Filename = $fileName;
        $image->Title = $this->faker->sentence;
        $image->IsSeed = true;
        $image->write();

        return $image;
    }

    /**
     * @param int $pc
     * @return bool
     */
    public function randomNull($pc = 10)
    {
        return rand(0, 100) < $pc;
    }

    /**
     *
     */
    public function unseed()
    {
        $dataObjects = $this->config()->DataObjects;

        // will miss seeds nested 2 recursions or deeper
        if (is_array($dataObjects)) {
            foreach ($dataObjects as $className => $data) {
                $this->deleteClassSeeds($className);
            }
        }

        $this->deleteClassSeeds('Image');

        $this->outputFormatter->flush();
    }

    /**
     * @param $className
     */
    private function deleteClassSeeds($className)
    {
        if (class_exists($className) && Object::has_extension($className, 'SeederExtension')) {
            $seedObjects = $className::get()->filter('IsSeed', true);
            $this->outputFormatter->deletingClassRecords($className, $seedObjects->Count());
            foreach ($seedObjects as $obj) {
                if ($obj instanceof Image || is_subclass_of($obj, 'Image')) {
                    try {
                        // will throw exception if file doesn't exist
                        $obj->delete();
                    } catch (Exception $e) {
                    }
                } else {
                    foreach ($obj->many_many() as $manyManyField => $type) {
                        try {
                            $obj->$manyManyField()->removeAll();
                        } catch (Exception $e) {
                        }
                    }

                    foreach ($obj->has_many() as $hasManyField => $type) {
                        try {
                            $obj->$hasManyField()->removeAll();
                        } catch (Exception $e) {
                        }
                    }

                    if ($obj instanceof SiteTree) {
                        $obj->deleteFromStage('Live');
                        $obj->deleteFromStage('Stage');
                    } else {
                        $obj->delete();
                    }
                }
            }
        }
    }

    /**
     * Returns a number within the range $options[max_$type, min_$type] or $options[$type] otherwise $default
     * @param $options
     * @param $type
     * @param int $default
     * @return int
     */
    public function calculateCount($options, $type, $default = 10)
    {
        $count = $default;
        if (isset($options['max_' . $type]) && is_numeric($options['max_' . $type])
            && isset($options['min_' . $type]) && is_numeric($options['min_' . $type])
        ) {
            $count = $this->faker->numberBetween($options['min_' . $type], $options['max_' . $type]);
        }
        if (isset($options[$type])) {
            $count = $options[$type];
        }
        return $count;
    }

    /**
     * @param bool $bool
     */
    public function ignoreCurrentRecords($bool = true)
    {
        $this->ignoreCurrentRecords = $bool;
    }

    /**
     * @param $obj
     */
    private function writeObject($obj)
    {
        if ($obj instanceof SiteTree) {
            $obj->writeToStage('Stage');

            $publish = isset($data['publish']) ? $data['publish'] : true;
            if ($publish !== false) {
                $obj->publish('Stage', 'Live');
            }
        } else {
            $obj->write();
        }
    }
}
