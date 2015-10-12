<?php

namespace LittleGiant\SilverStripeSeeder\Util;

class Field
{
    const FT_FIELD = 'db';
    const FT_HAS_ONE = 'has_one';
    const FT_HAS_MANY = 'has_many';
    const FT_MANY_MANY = 'many_many';
    const FT_ROOT = 'root';

    public $name;
    public $key;
    public $arguments = array();
    public $fieldType;
    public $dataType;
    public $fieldName;
    public $methodName;
    public $provider;
    public $explicit = false;
    public $count = 1;
    public $totalCount = 0;
    public $fields = array();
    public $hasOne = array();
    public $hasMany = array();
    public $manyMany = array();
    public $ancestry = array();
}

