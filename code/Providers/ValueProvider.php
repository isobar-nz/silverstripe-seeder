<?php

use LittleGiant\SilverStripeSeeder\Providers\Provider;

/**
 * Class ValueProvider
 */
class ValueProvider extends Provider
{
    /**
     * @var string
     */
    public static $shorthand = 'Value';

    /**
     * @param $argumentString
     * @return array
     */
    public static function parseOptions($argumentString)
    {
        return array(
            'value' => $argumentString,
        );
    }

    /**
     * @param $field
     * @param $state
     * @return int|mixed|string
     */
    protected function generateField($field, $state)
    {
        $value = $this->resolveValue($field, $state);

        if ($value instanceof \DataObject) {
            $value = $value->ID;
        }

        return $value;
    }

    /**
     * @param $field
     * @param $state
     * @return mixed|string
     */
    protected function generateOne($field, $state)
    {
        $value = $this->resolveValue($field, $state);
        return $value;
    }

    /**
     * @param $field
     * @param $state
     * @throws Exception
     * @returns null
     */
    protected function generateMany($field, $state)
    {
        throw new Exception('value provider does not support generating has many fields');
    }

    /**
     * @param $field
     * @param $state
     * @return mixed|string
     */
    private function resolveValue($field, $state)
    {
        if (empty($field->arguments['value'])) {
            return '';
        }

        $value = $field->arguments['value'];

        if (preg_match_all('/\{\$([^}]+)}/', $field->arguments['value'], $matches)) {
            for ($i = 0; $i < count($matches[0]); $i++) {
                $variable = $matches[1][$i];
                $variableValue = $this->resolveVariable($state, $variable);

                // when string is an object e.g '{$Up.Up}'
                if ($value === $matches[0][$i]) {
                    $value = $variableValue;
                } else {
                    $value = str_replace($matches[0][$i], $variableValue, $value);
                }
            }
        }

        return $value;
    }

    /**
     * @param $state
     * @param $variable
     * @return mixed
     */
    private function resolveVariable($state, $variable)
    {
        $variables = explode('.', $variable);

        if ($variable === 'i') {
            return $state->index();
        }

        $value = $state->object();
        foreach ($variables as $variable) {
            if ($variable === 'Up') {
                $state = $state->up();
                $value = $state->object();
            } else {
                // does this support has_one
                $value = $value->getField($variable);
            }
        }

        return $value;
    }
}
