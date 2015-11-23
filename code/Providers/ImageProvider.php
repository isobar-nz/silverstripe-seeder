<?php

use Faker\Factory;
use LittleGiant\SilverStripeSeeder\Providers\Provider;

/**
 * Class ImageProvider
 */
class ImageProvider extends Provider
{
    /**
     * @var
     */
    private $faker;

    /**
     * @var string
     */
    public static $shorthand = 'Image';

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->faker = Factory::create();
    }

    /**
     * @param $argumentString
     * @return array
     */
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

    /**
     * @param $field
     * @param $state
     * @throws Exception
     * @returns null
     */
    protected function generateField($field, $state)
    {
        throw new Exception('image provider does not support generating db fields');
    }

    /**
     * @param $field
     * @param $state
     * @return mixed
     */
    protected function generateOne($field, $state)
    {
        return $this->createImage($field, $state);
    }

    /**
     * @param $field
     * @param $state
     * @return array
     */
    protected function generateMany($field, $state)
    {
        $images = array();

        for ($i = 0; $i < $field->count; $i++) {
            $images[] = $this->createImage($field, $state);
        }

        return $images;
    }

    /**
     * @param $field
     * @param $upState
     * @return mixed
     */
    private function createImage($field, $upState)
    {
        $width = 600;
        $height = 400;

        if (!empty($field->options['height'])) {
            if (strpos($field->options['height'], ',') !== false) {
                $height = explode(',', $field->options['height']);
                $height = intval($this->faker->numberBetween(min($height), max($height)));
            } else {
                $height = intval($field->options['height']);
            }
        }

        if (!empty($field->options['width'])) {
            if (strpos($field->options['width'], ',') !== false) {
                $width = explode(',', $field->options['width']);
                $width = intval($this->faker->numberBetween(min($width), max($width)));
            } else {
                $width = intval($field->options['width']);
            }
        }

        $folder = Folder::find_or_make('Seeder');



        $file = file_get_contents("http://placehold.it/{$width}x{$height}");
        $fileName = uniqid('test-image') . '.jpg';
        file_put_contents($folder->getFullPath() . DIRECTORY_SEPARATOR . $fileName, $file);

//        $fileName = $this->faker->image($folder->getFullPath(), $width, $height);
//        $fileName = str_replace($folder->getFullPath() . DIRECTORY_SEPARATOR, '', $fileName);

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

