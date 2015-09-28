# silverstripe-seeder

## Basic Usage

    framework/sake seed [-f|--force] [-c|--class ClassName]
    framework/sake unseed

## Basic Configuration

    Seeder:
        create:
            [dataobject]:
                ignore:
                    - [field]
                nullable: true|false
                properties:
                    [field]: [string|int]
                    [has_one field]:
                        ... recurse
                    [has_many|many_many field]:
                        count: [int]
                        ... recurse
            [dataobject]:
                ...

For example:

    Seeder:
        create:
            Team:
                count: 2
                properties:
                    TeamMembers:
                        count: 10
                        properties:
                            Image:

Would create 2 teams with 10 team members each

## Providers

Providers add extra configuration to fields to control how to values are generated

### Basic provider configuration example
    Seeder:
        create:
            [dataobject]:
                properties:
                    [field]:
                        provider: [providername]
                        [argument]: [value]


For example:

    Seeder:
        create:
            Team:
                count: 2
                properties:
                    Title:
                        provider: 'ValueProvider'
                        value: 'Team Title'
                    TeamMembers:
                        count: 10
                        FirstName:
                            provider: 'DataTypeProvider'
                            type: 'firstname'
                        Image:
                            provider: 'ImageProvider'
                            width: 160
                            height: 100

### Provider shorthands

Providers have shorthands to ease configuration. If no provider shorthand is included the default provider is used, which is the ValueProvider. To shorten the above example:

    Seeder:
        create:
            Team:
                count: 2
                properties:
                    Title: 'value(Team Title)' or 'Team Title'
                    TeamMembers:
                        count: 10
                        FirstName: 'type(firstname)'
                        Image: 'image(160,100)'

# Advanced Configuration

## Provider types

### ValueProvider
The value given to the value provider can include variables.

    [field]:
        provider: 'ValueProvider'
        value: 'This is a {$variable}'

    [field]: 'This is a {$variable}'

Variables are taken from the current object, however no order is guarenteed and they may not be initialized yet. However there are special variables {$i} (the current index, in has many and many many relationships) and {$Up} (the parent object).

    [field]: '{$i} is the current index of the object'
    [field]: '{$Up.Title} is the parent object's title'
    [field]: '{$Up.Up.Title} is the parent's parent object's title'


