<?php

class Seeder extends Object
{
    /**
     * @var
     */
    private $faker;

    /**
     * @var array
     */
    private $fieldTypes = array(
        'firstName' => array('firstname'),
        'lastName' => array('lastname', 'surname'),
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
        'countryCode' => array('countrycode'),
        'lat' => array('lat', 'latitude'),
        'lng' => array('lng', 'longitude'),
        'link' => array('link', 'url'),
        'facebookLink' => array('facebook', 'facebooklink'),
        'googleplusLink' => array('googleplus', 'googlepluslink'),
        'twitterLink' => array('twitter', 'twitterlink'),
        'linkedinLink' => array('linkedin', 'linkedinlink'),
        'sort' => array('sort', 'sortorder'),
    );

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
                    $this->fakeClass($className, $data);
                }
            }
        }
    }

    /**
     * @param $className
     * @param $data
     */
    private function fakeClass($className, $data)
    {
        $fields = DataObject::custom_database_fields($className);

        if (!Object::has_extension($className, 'SeederExtension')) {
            error_log("'{$className}' does not have the 'SeederExtension'");
            return;
        }

        $count = 10;
        if (!empty($data['count'])) {
            $count = $data['count'];
        }

        echo "Faking {$count} '{$className}'", PHP_EOL;

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

                    if (!empty($options['nullable']) && $this->randomNull()) {
                        $obj->$field = null;
                    } else if ($type === 'Image') {
                        echo "Faking image for '{$className}'", PHP_EOL;
                        $obj->$field = $this->createImage($options);
                    }
                } else {
                    $options = isset($data['properties'][$field]) ? $data['properties'][$field] : array();
                    $obj->$field = $this->getSeedValue($className, $field, $type, $options);
                }
            }

            $obj->write();
        }
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

        if (!empty($options['type']) && isset($this->fieldTypes[$options['type']])) {
            $type = strtolower($options['type']);
        }

        $length = false;
        if (isset($options['min_length']) && is_numeric($options['min_length'])
            && isset($options['max_length']) && is_numeric($options['max_length'])) {
            $minLength = max(0, intval($options['min_length']));
            $maxLength = max(0, intval($options['max_length']));
            $length = $this->faker->numberBetween($minLength, $maxLength);
        }
        if (isset($options['length']) && is_numeric($options['length'])) {
            $length = intval($options['length']);
        }

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
        } else if ($type === 'htmltext') {
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
        } else if ($type === 'ss_datetime') {
            return $this->faker->dateTime();
        } else if ($type === 'text') {
            $length = $length === false ? 3 : $length;
            return $this->faker->paragraphs($length);
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

        error_log('unknown data type "' . $type . '"');
        return '';
    }

    /**
     * @param $options
     * @return int
     * @throws ValidationException
     * @throws null
     */
    public function createImage($options)
    {
        $path = BASE_PATH . '/assets/';

        $width = 640;
        $height = 480;

        if (isset($options['min_width']) && is_numeric($options['min_width']) && isset($options['max_width']) && is_numeric($options['max_width'])) {
            $minWidth = min($options['min_width'], $options['max_width']);
            $maxWidth = max($options['min_width'], $options['max_width']);
            $width = $this->faker->numberBetween($minWidth, $maxWidth);
        }

        if (isset($options['min_height']) && is_numeric($options['min_height']) && isset($options['max_height']) && is_numeric($options['max_height'])) {
            $minHeight = min($options['min_height'], $options['max_height']);
            $maxHeight = max($options['min_height'], $options['max_height']);
            $height = $this->faker->numberBetween($minHeight, $maxHeight);
        }

        $width = !empty($options['width']) && is_numeric($options['width']) ? $options['width'] : $width;
        $height = !empty($options['height']) && is_numeric($options['height']) ? $options['height'] : $height;

        $fileName = $this->faker->image($path, $width, $height);
        $fileName = str_replace(BASE_PATH . '/', '', $fileName);

        $image = new Image();
        $image->Filename = $fileName;
        $image->Title = $this->faker->sentence;
        $image->IsSeed = true;
        $image->write();

        return $image->ID;
    }

    /**
     * @param int $pc
     * @return bool
     */
    public function randomNull($pc = 10)
    {
        return rand(0, 100) < $pc;
    }

    public function unseed()
    {
        $dataObjects = $this->config()->DataObjects;

        if (is_array($dataObjects)) {
            foreach ($dataObjects as $className => $data) {
                $this->deleteClassSeeds($className);
            }
        }

        $this->deleteClassSeeds('Image');
    }

    /**
     * @param $className
     */
    private function deleteClassSeeds($className)
    {
        if (class_exists($className) && Object::has_extension($className, 'SeederExtension')) {
            $seedObjects = $className::get()->filter('IsSeed', true);
            echo "Cleaning up seeds for '{$className}'", PHP_EOL;
            foreach ($seedObjects as $obj) {
                if ($className === 'Image') {
                    try {
                        // will throw exception if file doesn't exist
                        $obj->delete();
                    } catch (Exception $e) {
                    }
                } else {
                    $obj->delete();
                }
            }
        }
    }
}
