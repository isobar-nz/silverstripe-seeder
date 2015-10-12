<?php

use LittleGiant\SilverStripeSeeder\Providers\Provider;

class SortProvider extends Provider
{
    public static $shorthand = 'sort';

    private static $class = array();

    private static $cache = array();

    protected function generateField($field, $state)
    {
        if (!$state->up() || !$state->up()->object()) {
            return 0;
        }

        $obj = $state->up()->object();
        $className = $obj->class;
        if (!isset(self::$class[$className])) {
            $ancestry = $className->getClassAncestry();
            foreach ($ancestry as $ancestor) {
                $fields = Object::custom_database_fields($ancestor);
                if (isset($fields[$field->name])) {
                    self::$class[$className] = $ancestor;
                    break;
                }
            }
        }

        $sortClass = self::$class[$className];

        if (!isset(self::$cache[$sortClass])) {
            self::$cache[$sortClass] = $sortClass::get()->max($field);
        }

        $sort = self::$cache[$sortClass] + 1;
        self::$cache[$sortClass] = $sort;
        return $sort;
    }
}
