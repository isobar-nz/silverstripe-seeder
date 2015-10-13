<?php

use LittleGiant\SilverStripeSeeder\Providers\Provider;

class ProviderCache extends Provider
{
    public $provider;

    private $cacheSize;

    private $cache = array();

    public function __construct($provider, $cacheSize)
    {
        parent::__construct();
        $this->provider = $provider;
        $this->setCacheSize($cacheSize);
    }

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

    public function setCacheSize($size)
    {
        if ($size < 1) {
            $size = 1;
        }
        $this->cacheSize = $size;
    }

    protected function generateField($field, $state)
    {
        // error
    }
}
