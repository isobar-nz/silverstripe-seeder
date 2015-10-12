<?php

use LittleGiant\SilverStripeSeeder\Providers\Provider;

class SortProvider extends Provider
{
    public static $shorthand = 'sort';

    private static $classCache = array();

    private static $sortCache = array();

    protected function generateField($field, $state)
    {
        if (!$state->up() || !$state->up()->object()) {
            return 0;
        }

        $obj = $state->up()->object();
        $className = $obj->class;
        if (!isset(self::$classCache[$className])) {
            $ancestry = $className->getClassAncestry();
            foreach ($ancestry as $ancestor) {
                $fields = Object::custom_database_fields($ancestor);
                if (isset($fields[$field->name])) {
                    self::$classCache[$className] = $ancestor;
                    break;
                }
            }
        }

        $sortClass = self::$classCache[$className];

        if (!isset(self::$sortCache[$sortClass])) {
            self::$sortCache[$sortClass] = $sortClass::get()->max($field);
        }

        $sort = self::$sortCache[$sortClass] + 1;
        self::$sortCache[$sortClass] = $sort;
        return $sort;
    }
}
