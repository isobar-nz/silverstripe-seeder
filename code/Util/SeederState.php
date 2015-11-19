<?php

namespace Seeder\Util;

/**
 * Class SeederState
 * @package Seeder\Util
 */
class SeederState
{
    /**
     * @var SeederState
     */
    private $up;
    /**
     * @var Field
     */
    private $field;
    /**
     * @var \DataObject
     */
    private $object;
    /**
     * @var int
     */
    private $index;

    /**
     * @param Field $field
     * @param \DataObject $object
     * @param int $index
     * @param SeederState $up
     */
    public function __construct(Field $field = null, \DataObject $object = null, $index = 0, SeederState $up = null)
    {
        $this->field = $field;
        $this->object = $object;
        $this->index = $index;
        $this->up = $up;
    }

    /**
     * @return SeederState
     */
    public function up()
    {
        return $this->up;
    }

    /**
     * @return int
     */
    public function index()
    {
        return $this->index;
    }

    /**
     * @return Field
     */
    public function field()
    {
        return $this->field;
    }

    /**
     * @return \DataObject
     */
    public function object()
    {
        return $this->object;
    }

    /**
     * @param Field $field
     * @param \DataObject $dataObject
     * @param int $index
     * @return SeederState
     */
    public function down(Field $field, \DataObject $dataObject = null, $index = 0)
    {
        return new SeederState($field, $dataObject, $index, $this);
    }

    /**
     * @param $object
     * @return bool
     */
    public function isAncestor($object)
    {
        if (!$this->object) {
            return false;
        }
        return $this->object === $object || ($this->up && $this->up->isAncestor($object));
    }
}
