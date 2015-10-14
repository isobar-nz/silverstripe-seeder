<?php

/**
 * Class SeedRecord
 */
class SeedRecord extends DataObject
{
    /**
     * @var array
     */
    private static $db = array(
        'SeedClassName' => 'Varchar(255)',
        'SeedID' => 'Int',
        'Key' => 'Varchar(60)',
        'Root' => 'Boolean',
    );
}
