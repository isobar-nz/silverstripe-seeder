<?php

use LittleGiant\SilverStripeSeeder\Providers\Provider;

/**
 * Class SortProvider
 */
class SortProvider extends Provider
{
    /**
     * @var string
     */
    public static $shorthand = 'sort';

    /**
     * @var array
     */
    private static $classCache = array();

    /**
     * @var array
     */
    private static $sortCache = array();

    /**
     * @param $field
     * @param $state
     * @return int
     */
    protected function generateField($field, $state)
    {
        if (!$state->up() || !$state->up()->object()) {
            return 0;
        }

        $obj = $state->up()->object();
        $className = $obj->class;
        if (!isset(self::$classCache[$className])) {
            $ancestry = singleton($className)->getClassAncestry();
            foreach ($ancestry as $ancestor) {
                $fields = DataObject::custom_database_fields($ancestor);
                if (isset($fields[$field->name])) {
                    self::$classCache[$className] = $ancestor;
                    break;
                }
            }
        }

        $sortClass = self::$classCache[$className];

        if (!isset(self::$sortCache[$sortClass])) {
            self::$sortCache[$sortClass] = $sortClass::get()->max($field->name);
        }

        $sort = self::$sortCache[$sortClass] + 1;
        self::$sortCache[$sortClass] = $sort;
        return $sort;
    }
}
