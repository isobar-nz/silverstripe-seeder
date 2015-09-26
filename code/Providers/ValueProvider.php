<?php

use LittleGiant\SilverStripeSeeder\Providers\Provider;

class ValueProvider extends Provider
{
    protected function generateField($field, $state)
    {
        $value = $this->resolveValue($field, $state);

        if ($value instanceof \DataObject) {
            $value = $value->ID;
        }

        return $value;
    }

    protected function generateHasOneField($field, $state)
    {
        $value = $this->resolveValue($field, $state);
        return $value;
    }

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
                $value = $value->$variable;
            }
        }

        return $value;
    }
}
