<?php

namespace LittleGiant\SilverStripeSeeder\Providers;

abstract class Provider extends Object
{
    private static $shorthand = 'default';

    private static $order = 1;

    protected $writer;

    abstract public function generate($field, $state);

    public function get_shorthand()
    {
        return $this->config()->get('shorthand');
    }

    public function get_order()
    {
        return $this->config()->get('order');
    }

    public function setWriter($writer)
    {
        $this->writer = $writer;
    }
}