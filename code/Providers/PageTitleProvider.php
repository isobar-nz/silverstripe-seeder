<?php

use LittleGiant\SilverStripeSeeder\Providers\Provider;

class PageTitleProvider extends Provider
{
    public static $shorthand = 'PageTitle';

    protected function generateField($field, $state)
    {
        if (!$state->up() || !$state->up->object()) {
            return 'Page Title';
        }

        $page = $state->up()->object();
        $name = str_replace(array('Page', 'Holder'), '', $page->class);
        return $name;
    }
}
