<?php

use LittleGiant\SilverStripeSeeder\Providers\Provider;

/**
 * Class PageTitleProvider
 */
class PageTitleProvider extends Provider
{
    /**
     * @var string
     */
    public static $shorthand = 'PageTitle';

    /**
     * @param $field
     * @param $state
     * @return mixed|string
     */
    protected function generateField($field, $state)
    {
        if (!$state->up() || !$state->up()->object()) {
            return 'Page Title';
        }

        $page = $state->up()->object();

        $name = str_replace(array('Page', 'Holder'), '', $page->class);
        $name = FormField::name_to_label($name);

        return $name;
    }
}
