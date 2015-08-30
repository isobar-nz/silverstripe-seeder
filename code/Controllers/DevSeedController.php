<?php

/**
 * Class DevSeedController
 */
class DevSeedController extends Controller
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
        $seeder = new Seeder();

        $force = $this->request->getVar('force');
        if ($force === '1' || $force = 'all') {
            $seeder->ignoreCurrentRecords(true);
        }

        $seeder->seed();
    }
}
