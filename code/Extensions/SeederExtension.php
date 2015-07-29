<?php

/**
 * Class SeederExtension
 */
class SeederExtension extends DataExtension
{
    /**
     * @var array
     */
    private static $db = array(
        'IsSeed' => 'Boolean',
    );

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName('IsSeed');
    }
}