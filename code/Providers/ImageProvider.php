<?php

use Faker\Factory;
use LittleGiant\SilverStripeSeeder\Providers\Provider;

class ImageProvider extends Provider
{
    private $faker;

    public static $shorthand = 'Image';

    public function __construct()
    {
        parent::__construct();
        $this->faker = Factory::create();
    }

    public static function parseOptions($argumentString)
    {
        $arguments = array_map(function ($arg) {
            return intval(trim($arg));
        }, explode(',', $argumentString));

        $options = array();
        if (count($arguments) >= 2) {
            $options['width'] = $arguments[0];
            $options['height'] = $arguments[1];
        } else if (count($arguments) && $arguments[0]) {
            $options['width'] = $arguments[0];
            $options['height'] = $arguments[0];
        }

        return $options;
    }

    protected function generateField($field, $state)
    {
        throw new Exception('image provider does not support generating db fields');
    }

    protected function generateHasOneField($field, $state)
    {
        return $this->createImage($field, $state);
    }

    protected function generateHasManyField($field, $state)
    {
        $images = array();
        $count = 1;
        if (!empty($field->arguments['count'])) {
            $count = intval($field->arguments['count']);
        }

        for ($i = 0; $i < $count; $i++) {
            $images[] = $this->createImage($field, $state);
        }

        return $images;
    }

    protected function generateManyManyField($field, $state)
    {
        return $this->generateHasManyField($field, $state);
    }

    private function createImage($field, $upState)
    {
        $width = 600;
        $height = 400;

        if (!empty($field->arguments['height'])) {
            if (strpos($field->arguments['height'], ',') !== false) {
                $height = explode(',', $field->arguments['height']);
                $height = intval($this->faker->numberBetween(min($height), max($height)));
            } else {
                $height = intval($this->arguments['height']);
            }
        }

        if (!empty($field->arguments['width'])) {
            if (strpos($field->arguments['width'], ',') !== false) {
                $width = explode(',', $field->arguments['width']);
                $width = intval($this->faker->numberBetween(min($width), max($width)));
            } else {
                $width = intval($this->arguments['width']);
            }
        }

        $folder = Folder::find_or_make('Seeder');
        $fileName = $this->faker->image($folder->getFullPath(), $width, $height);
        $fileName = str_replace($folder->getFullPath() . DIRECTORY_SEPARATOR, '', $fileName);

        $imageClassName = $field->dataType;
        $image = new $imageClassName();
        $image->Filename = $fileName;
        $image->Title = $this->faker->Sentence;
        $image->setParentID($folder->ID);

        $state = $upState->down($field, $image);
        $this->writer->write($image, $field, $state, true);

        return $image;
    }
}

