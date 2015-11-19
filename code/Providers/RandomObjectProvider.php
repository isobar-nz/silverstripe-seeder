<?php

use LittleGiant\SilverStripeSeeder\Providers\Provider;

/**
 * Class RandomObjectProvider
 */
class RandomObjectProvider extends Provider
{
    /**
     * @var string
     */
    public static $shorthand = 'random';

    /**
     * @param $field
     * @param $state
     * @throws Exception
     * @returns null
     */
    protected function generateField($field, $state)
    {
        throw new Exception('random object provider does not support generating db fields');
    }

    /**
     * @param $field
     * @param $state
     * @return mixed
     * @throws Exception
     */
    protected function generateOne($field, $state)
    {
        $args = $field->arguments['arguments'];

        $className = $field->dataType;
        if (count($args) && !empty($args[0])) {
            $className = $args[0];
        }

        $object = $className::get()->first();

        if (!$object) {
            SS_Log::log("random for {$className} not found", SS_Log::WARN);
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
        $args = $field->arguments['arguments'];

        $className = $field->dataType;
        if (count($args) && !empty($args[0])) {
            $className = $args[0];
        }

        $count = 1;
        if (count($args) > 1 && !empty($args[1])) {
            $count = intval($args[1]);
        }

        return $className::get()->sort('RAND()')->limit($count);
    }
}
