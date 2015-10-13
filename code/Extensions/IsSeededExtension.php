<?php

class IsSeededExtension extends Extension
{
    private $isSeeded = false;

    public function isSeeded()
    {
        return $this->isSeeded;
    }

    public function setIsSeeded($bool = true)
    {
        $this->isSeeded = $bool;
    }
}
