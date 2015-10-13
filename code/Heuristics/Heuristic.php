<?php

namespace LittleGiant\SilverStripeSeeder\Heuristics;

use ProviderCache;

class Heuristic
{
    public $name;

    private $options = array();
    private $ignore = null;
    private $cache = false;
    private $noCache = false;

    private $conditions = array();

    public function __construct($name)
    {
        $this->name = $name;
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
        if (!$field->explicit) {
            // if a new provider then reset options
            if (isset($this->options['provider'])) {
                $providerClassName = $this->options['provider'];
                $provider = new $providerClassName();
                if ($field->provider instanceof ProviderCache) {
                    $field->provider->provider = $provider;
                } else {
                    $field->provider = $provider;
                }
                $field->arguments = $this->options;
            } else {
                $field->arguments = array_merge($field->arguments, $this->options);
            }

            // if ignore is specified then set on field
            if ($this->ignore !== null) {
                $field->ignore = $this->ignore;
            }
        }

        if ($this->cache !== false) {
            if ($field->provider instanceof ProviderCache) {
                $field->provider->setCacheSize($this->cache);
            } else {
                $cache = new ProviderCache($field->provider, $this->cache);
                $field->provider = $cache;
            }
        }

        if ($this->noCache && $field->provider instanceof ProviderCache) {
            $field->provider = $field->provider->provider;
        }
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }

    public function setIgnore($ignore = false)
    {
        $this->ignore = $ignore;
    }

    public function setCache($cache = false)
    {
        $this->cache = $cache;
    }

    public function setNoCache($noCache = false)
    {
        $this->noCache = $noCache;
    }
}
