<?php

use LittleGiant\SilverStripeSeeder\HtmlOutputFormatter;

/**
 * Class DevUnseedController
 */
class DevUnseedController extends Controller
{
    /**
     * @var array
     */
    private static $allowed_actions = array(
        'index',
    );

    /**
     *
     */
    public function index()
    {
        $seeder = Seeder::create(new HtmlOutputFormatter());

        $seeder->unseed();
    }
}

