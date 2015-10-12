<?php

use LittleGiant\SilverStripeSeeder\Providers\Provider;

class URLSegmentProvider extends Provider
{
    public static $shorthand = 'URLSegment';

    protected function generateField($field, $state)
    {
        if (!$state->up() || !$state->up->object()) {
            return uniqid('Title');
        }

        $page = $state->up()->object();

        if ($field->totalCount > 1) {
            return Convert::raw2url(uniqid($page->Title));
        }

        $page = $state->up()->object();
        $name = str_replace(array('Page', 'Holder'), '', $page->class);

        // check if already exists
        $url = Convert::raw2url($name);
        if (SiteTree::get()->filter('URLSegment', $url)->Count()) {
            return uniqid($url);
        }

        return $url;
    }
}
