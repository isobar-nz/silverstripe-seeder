<?php

namespace Seeder\Util;

/**
 * Class Field
 * @package Seeder\Util
 */
class Field
{
    /**
     *
     */
    const FT_FIELD = 'db';

    /**
     *
     */
    const FT_HAS_ONE = 'has_one';

    /**
     *
     */
    const FT_HAS_MANY = 'has_many';

    /**
     *
     */
    const FT_MANY_MANY = 'many_many';

    /**
     *
     */
    const FT_ROOT = 'root';

    /**
     * @var
     */
    public $name;

    /**
     * @var
     */
    public $parent;

    /**
     * @var
     */
    public $key;

    /**
     * @var array
     */
    public $options = array();

    /**
     * @var
     */
    public $fieldType;

    /**
     * @var
     */
    public $dataType;

    /**
     * @var
     */
    public $fieldName;

    /**
     * @var
     */
    public $methodName;

    /**
     * @var
     */
    public $provider;

    /**
     * @var bool
     */
    public $explicit = false;

    /**
     * @var int
     */
    public $count = 1;

    /**
     * @var int
     */
    public $totalCount = 0;

    /**
     * @var bool
     */
    public $ignore = false;

    /**
     * @var array
     */
    public $fields = array();

    /**
     * @var array
     */
    public $hasOne = array();

    /**
     * @var array
     */
    public $hasMany = array();

    /**
     * @var array
     */
    public $manyMany = array();

    /**
     * @var array
     */
    public $ancestry = array();
}
