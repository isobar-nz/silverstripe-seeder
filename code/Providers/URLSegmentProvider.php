<?php

use LittleGiant\SilverStripeSeeder\Providers\Provider;

/**
 * Class URLSegmentProvider
 */
class URLSegmentProvider extends Provider
{
    /**
     * @var string
     */
    public static $shorthand = 'URLSegment';

    /**
     * @param $field
     * @param $state
     * @return string
     */
    protected function generateField($field, $state)
    {
        if (!$state->up() || !$state->up()->object()) {
            return uniqid('url');
        }

        $page = $state->up()->object();

        if ($field->totalCount > 1) {
            return Convert::raw2url(uniqid($page->Title));
        }

        $page = $state->up()->object();
        $name = str_replace(array('Page', 'Holder'), '', $page->class);
        $name = FormField::name_to_label($name);

        // check if already exists
        $url = Convert::raw2url($name);
        if (SiteTree::get()->filter('URLSegment', $url)->Count()) {
            return uniqid($url);
        }

        return $url;
    }
}
