<?php

namespace LittleGiant\SilverStripeSeeder\Util;

class CounterTree
{
    private $baseNodes = array();

    public function record($ancestry)
    {
        $className = array_pop($ancestry);
        if (!isset($this->baseNodes[$className])) {
            $this->baseNodes[$className] = new CounterTreeNode($className);
        }
        $node = $this->baseNodes[$className];
        $node->record($ancestry);
    }

    public function getTree()
    {
        $nodes = array();
        foreach ($this->baseNodes as $node) {
            $nodes[] = $node->getTree();
        }
        return $nodes;
    }
}

class CounterTreeNode
{
    private $value;
    private $count = 0;
    private $children = array();

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function record(&$ancestry)
    {
        if (empty($ancestry)) {
            $this->count++;
        } else {
            $className = array_pop($ancestry);
            if (!isset($this->children[$className])) {
                $this->children[$className] = new CounterTreeNode($className);
            }
            $node = $this->children[$className];
            $node->record($ancestry);
        }
    }

    public function getTree()
    {
        $children = array();
        foreach ($this->children as $child) {
            $children[] = $child->getTree();
        }

        return array(
            'class' => $this->value,
            'count' => $this->count,
            'children' => $children,
        );
    }
}
