<?php

namespace LittleGiant\SilverStripeSeeder\Util;

class SeederState
{
    private $up;
    private $field;
    private $object;
    private $index;

    public function __construct(Field $field = null, \DataObject $object = null, $index = 0, SeederState $up = null)
    {
        $this->field = $field;
        $this->object = $object;
        $this->index = $index;
        $this->up = $up;
    }

    public function up()
    {
        return $this->up;
    }

    public function index()
    {
        return $this->index;
    }

    public function field()
    {
        return $this->field;
    }

    public function object()
    {
        return $this->object;
    }

    public function down(Field $field, \DataObject $dataObject = null, $index = 0)
    {
        return new SeederState($field, $dataObject, $index, $this);
    }

    public function isAncestor($object)
    {
        if (!$this->object) {
            return false;
        }
        return $this->object === $object || ($this->up && $this->up->isAncestor($object));
    }
}
