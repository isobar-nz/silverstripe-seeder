<?php

namespace Seeder;

/**
 * Class ProviderCache
 * @package Seeder
 */
class ProviderCache extends Provider
{
    /**
     * @var
     */
    public $provider;

    /**
     * @var
     */
    private $cacheSize;

    /**
     * @var array
     */
    private $cache = array();

    /**
     * @param $provider
     * @param $cacheSize
     */
    public function __construct($provider, $cacheSize)
    {
        parent::__construct();
        $this->provider = $provider;
        $this->setCacheSize($cacheSize);
    }

    /**
     * @param $field
     * @param $state
     * @return mixed
     */
    public function generate($field, $state)
    {
        if (count($this->cache) < $this->cacheSize) {
            $result = $this->provider->generate($field, $state);
            $this->cache[] = $result;
            return $result;
        }

        $key = array_rand($this->cache);
        return $this->cache[$key];
    }

    /**
     * @param $size
     */
    public function setCacheSize($size)
    {
        $this->cacheSize = max($size, 1);
    }

    /**
     * @param $field
     * @param $state
     * @returns null
     */
    protected function generateField($field, $state)
    {
        // error
    }
}
