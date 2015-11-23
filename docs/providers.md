Providers
=========

Providers are an easy way to customise what data is generated.

## Creating a provider

Here is an example provider class

``` php
class MyProvider extends \Seeder\Provider
{
    // the case insensitive shorthand to be used e.g myprovider(arg1, arg2...), required
    public static $shorthand = 'MyProvider';

    // called to generate `db` fields
    protected function generateField($field, $state)
    {
        return 'Some value';
    }

    // called to generate `has_one` fields
    protected function generateOne($field, $state)
    {
        $object = new MyDataObject();
        $object->write();
        return $object;
    }

    // called to generate `has_many` and `many_many` fields
    protected function generateMany($field, $state)
    {
        return MyDataObject::get()->limit($field->options['arguments'][0]);
    }
}
```

For the seeder to pick up the shorthand it must be added to config

``` yaml
Seeder:
    providers:
        - MyProvider
```
