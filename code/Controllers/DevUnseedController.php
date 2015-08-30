<?php

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
        $seeder = Unseeder::create();

        $seeder->unseed();
    }
}

