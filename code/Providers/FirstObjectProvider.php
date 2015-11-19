<?php

use LittleGiant\SilverStripeSeeder\Providers\Provider;

/**
 * Class FirstObjectProvider
 */
class FirstObjectProvider extends Provider
{
    /**
     * @var string
     */
    public static $shorthand = 'First';

    /**
     * @param $field
     * @param $state
     * @throws Exception
     * @returns null
     */
    protected function generateField($field, $state)
    {
        throw new Exception('first object provider does not support generating db fields');
    }

    /**
     * @param $field
     * @param $state
     * @return mixed
     * @throws Exception
     */
    protected function generateOne($field, $state)
    {
        $className = $field->dataType;

        $args = $field->arguments['arguments'];
        if (count($args) && !empty($args[0])) {
            $className = $args[0];
        }

        if (!class_exists($className)) {
            throw new Exception("class '{$className}' does not exist");
        }

        $object = $className::get()->first();

        if (!$object) {
            SS_Log::log("no instances found for '{$className}' not found", SS_Log::WARN);
        }

        return $object;
    }

    /**
     * @param $field
     * @param $state
     * @return mixed
     * @throws Exception
     */
    protected function generateMany($field, $state)
    {
        return $this->generateOne($field, $state);
    }
}
