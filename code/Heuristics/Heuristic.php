<?php

namespace LittleGiant\SilverStripeSeeder\Heuristics;

class Heuristic
{
    public $name;

    private $options;

    private $conditions = array();

    public function __construct($name, $options)
    {
        $this->name = $name;
        $this->options = $options;
    }

    public function addCondition($condition)
    {
        $this->conditions[] = $condition;
    }

    public function match($field)
    {
        foreach ($this->conditions as $condition) {
            if (!$condition->match($field)) {
                return false;
            }
        }
        return true;
    }

    public function getSpecificity()
    {
        return count($this->conditions);
    }

    public function apply($field)
    {
        if (isset($this->options['provider'])) {
            $providerClassName = $this->options['provider'];
            $field->provider = new $providerClassName();
        }

        $field->arguments = $this->options;
    }
}
