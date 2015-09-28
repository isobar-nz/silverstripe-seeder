<?php

use Faker\Factory;
use LittleGiant\SilverStripeSeeder\Providers\Provider;

class DataTypeProvider extends Provider
{
    public static $shorthand = 'Type';

    private $faker;

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
        'sort' => array('sort', 'sortorder'),
    );

    private $dataType = array(
        'boolean',
        'currency',
        'decimal',
        'percentage',
        'int',
        'date',
        'time',
        'ss_datetime',
        'htmlvar',
        'htmltext',
        'varchar',
        'text',
    );

    public function __construct()
    {
        parent::__construct();
        $this->faker = Factory::create();
    }

    public static function parseOptions($argumentString)
    {
        return array(
            'type' => strtolower(trim($argumentString)),
        );
    }


    protected function generateField($field, $state)
    {
        $dataType = strtolower($field->dataType);
        $name = strtolower($field->name);
        $args = $field->arguments;

        foreach ($this->fieldTypes as $fieldType => $names) {
            if (in_array($name, $names)) {
                $dataType = $fieldType;
            }
        }

        if (!empty($field->arguments['type'])) {
            $type = strtolower($field->arguments['type']);
            if (isset($this->fieldTypes[$type]) || in_array($type, $this->dataType)) {
                $dataType = $type;
            }
        }

        // named fields
        if ($dataType === 'firstname') {
            return $this->faker->firstName();
        } else if ($dataType === 'lastname') {
            return $this->faker->lastName;
        } else if ($dataType === 'email') {
            return $this->faker->safeEmail;
        } else if ($dataType === 'phone') {
            return $this->faker->phoneNumber;
        } else if ($dataType === 'company') {
            return $this->faker->company;
        } else if ($dataType === 'address') {
            return $this->faker->address;
        } else if ($dataType === 'address1') {
            return $this->faker->streetAddress;
        } else if ($dataType === 'address2') {
            return $this->faker->secondaryAddress;
        } else if ($dataType === 'city') {
            return $this->faker->city;
        } else if ($dataType === 'postcode') {
            return $this->faker->postcode;
        } else if ($dataType === 'state') {
            return $this->faker->state;
        } else if ($dataType === 'country') {
            return $this->faker->country;
        } else if ($dataType === 'countrycode') {
            return $this->faker->countryCode;
        } else if ($dataType === 'lat') {
            return $this->faker->latitude;
        } else if ($dataType === 'lng') {
            return $this->faker->longitude;
        } else if ($dataType === 'link') {
            return $this->faker->url;
        } else if ($dataType === 'sort') {
            return 0;

        // data type fields
        } else if ($dataType === 'boolean') {
            return array_rand(array(true, false));
        } else if ($dataType === 'currency') {
            $min = 0;
            $max = 1000;
            if (!empty($args['range'])) {
                $limits = array_map(function ($limit) {
                    return floatval($limit);
                }, explode(',', $args['range']));
                $min = min($limits);
                $max = min($limits);
            }
            return $this->faker->randomFloat(2, $min, $max);
        } else if ($dataType === 'date') {
            // todo
            return date('Y-m-d');
        } else if ($dataType === 'time') {
            // todo
            return date('H:i:s');
        } else if ($dataType === 'ss_datetime') {
            // todo
            return date('Y-m-d H:i:s');
        } else if (strpos($dataType, 'decimal') === 0) {
            $min = 0;
            $max = 1000;
            $decimals = 4;
            if (!empty($args['range'])) {
                $limits = array_map(function ($limit) {
                    return floatval($limit);
                }, explode(',', $args['range']));
                $min = min($limits);
                $max = min($limits);
            }
            if (!empty($args['decimals'])) {
                $decimals = intval($args['decimals']);
            }
            return $this->faker->randomFloat($decimals, $min, $max);
        } else if ($dataType === 'int') {
            $min = 0;
            $max = PHP_INT_MAX;
            if (!empty($args['range'])) {
                $limits = array_map(function ($limit) {
                    return intval($limit);
                }, explode(',', $args['range']));
                $min = min($limits);
                $max = min($limits);
            }
            return $this->faker->numberBetween($min, $max);
        } else if (strpos($dataType, 'enum') === 0) {
            $values = singleton($state->field()->dataType)
                ->dbObject($field->name)
                ->enumValues();
            return array_rand($values);
        } else if (strpos($dataType, 'htmltext') === 0) {
            // todo
            return '<p>TODO</p>';
        } else if (strpos($dataType, 'htmlvarchar') === 0) {
            // todo
            return '<p>TODO</p>';
        } else if ($dataType === 'text') {
            $count = 3;
            if (!empty($args['count'])) {
                if (strpos($args['count'], ',') !== false) {
                    $limits = array_map(function ($limit) {
                        return intval($limit);
                    }, explode(',', $args['count']));
                    $min = min($limits);
                    $max = min($limits);
                    // todo check whether inclusive
                    $count = $this->faker->numberBetween($min, $max);
                } else {
                    $count = intval($args['count']);
                }
            }
            return implode(PHP_EOL, $this->faker->paragraphs($count));
        } else if (strpos($dataType, 'varchar') !== false) {
            $length = 60;
            preg_match('/\(([0-9]*)\)/', $dataType, $matches);
            if ($matches) {
                $length = intval($matches[1]);
            }
            if (isset($args['length'])) {
                $length = intval($args['length']);
            }
            return $this->faker->text($length);
        }

        throw new Exception("unknown data type '{$field->dataType}'");
    }
}

