<?php

namespace LittleGiant\SilverStripeSeeder\Util;

class Check
{
    static function fileToUrlMapping()
    {
        global $_FILE_TO_URL_MAPPING;

        $found = false;

        foreach ($_FILE_TO_URL_MAPPING as $path => $domain) {
            if (file_exists($path) && is_dir($path)) {
                $last = substr($path, -1);
                if ($last === '/' || $last === '\\') {
                    echo PHP_EOL;
                    echo "WARNING: The path '{$path}' set in \$_FILE_TO_URL_MAPPING is invalid.", PHP_EOL;
                    echo "WARNING: Please remove the last '{$last}' from the path", PHP_EOL, PHP_EOL;
                } else {
                    $found = true;
                }
            }
        }

        return $found;
    }
}

