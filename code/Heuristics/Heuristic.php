<?php

namespace LittleGiant\SilverStripeSeeder\Heuristics;

use ProviderCache;

/**
 * Class Heuristic
 * @package LittleGiant\SilverStripeSeeder\Heuristics
 */
class Heuristic
{
    /**
     * @var
     */
    public $name;

    /**
     * @var array
     */
    private $options = array();
    /**
     * @var null
     */
    private $ignore = null;
    /**
     * @var bool
     */
    private $cache = false;
    /**
     * @var bool
     */
    private $noCache = false;

    /**
     * @var array
     */
    private $conditions = array();

    /**
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param $condition
     */
    public function addCondition($condition)
    {
        $this->conditions[] = $condition;
    }

    /**
     * @param $field
     * @return bool
     */
    public function match($field)
    {
        foreach ($this->conditions as $condition) {
            if (!$condition->match($field)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return int
     */
    public function getSpecificity()
    {
        return count($this->conditions);
    }

    /**
     * @param $field
     */
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

    /**
     * @param $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @param bool $ignore
     */
    public function setIgnore($ignore = false)
    {
        $this->ignore = $ignore;
    }

    /**
     * @param bool $cache
     */
    public function setCache($cache = false)
    {
        $this->cache = $cache;
    }

    /**
     * @param bool $noCache
     */
    public function setNoCache($noCache = false)
    {
        $this->noCache = $noCache;
    }
}
