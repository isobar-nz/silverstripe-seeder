# silverstripe-seeder

## Basic Usage

    framework/sake seed [-f|--force] [-c|--class ClassName]
    framework/sake unseed


`--force` do not take into account current seeds when calculating how many records to create
`--class ClassName` only create seeds for the given ClassName


## Basic Configuration

The seeder will populate db fields with sensible defaults. Has\_one, has\_many and many\_many relations need to be explicitly set in properties

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

### DataTypeProvider
Data type provider matches the field name to a common list of fields, if there aren't any matches it will fall back to providing a sensible value based on the data type of the database field. This is the default provider for unspecified database fields.

	[field]:
		provider: 'DataTypeProvider'
		type: [string:type]

#### Type options

	- firstname
	- lastname
	- email
	- phone
	- company
	- address
	- address1
	- address2
	- city
	- postcode
	- state
	- country
	- countrycode
	- lat
	- lng
	- link
	- boolean
	- currency [range]
	- decimal [range]
	- percentage
	- int [range]
	- date
	- time
	- ss_datetime
	- htmlvar
	- htmltext
	- varchar [length]
	- text [count]

	[range] allows you to specify the range of generated numbers e.g 10,30
	[count] allows you to control the number of paragraphs e.g 1,3
	[length] allows you to control the max number of characters e.g 50

#### Example

	Seeder:
		create:
			Person:
				properties:
					FirstName:
						length: 60 # max length is 60
					Bio:
						count: 1,3 # 1 to 3 paragraphs
					Age:
						range: 21,30 # an age between 21 and 30

### FakerProvider
Populates fields by a given [Faker](https://github.com/fzaninotto/Faker) property or method

	[field]:
		provider: 'FakerProvider'
		type: [string:property or method]
		arguments:
			- argument 1
			- argument 2

		[field]: 'faker(property)'

		[field]: 'faker(method,arg1,arg2...)'

#### Example:

	Seeder:
		create:
			Person:
				FirstName: 'faker(name)'
				Address: 'faker(city)'
				Bio: 'faker(text,300)'

### ImageProvider

	[field]:
		provider: 'ImageProvider'
		width: [int:width] or [int:min,int:max] e.g 100,450
		height: [int:height] or [int:min,int:max] e.g 10,30

	[field]: 'image(width,height)'

Downloads a random image and stores it in the `assets/Uploads/Seeder` directory


### ObjectProvider

Select an object for a has one relationship or a list of random objects for a has many or many many.

	[field]:
		provider: 'ObjectProvider'
		class: [string:dataobject class]
		count: [int]

	[field]: 'object(class)' # has_one

	[field]: 'object(class,count)' # has_many and many_many

##### Example

	Seeder:
		create:
			ProductPage:
				count: 10
				properties:
					Parent: 'object(ProductHolder)'
					RelatedProducts: 'object(ProductPage,4)'


### ValueProvider

The value given to the value provider can include variables.

    [field]:
        provider: 'ValueProvider'
        value: 'This is a {$variable}'

    [field]: 'This is a {$variable}'

Variables are taken from the current object, however no order is guarenteed and they may not be initialized yet. However there are special variables {\$i} (the current index, in has many and many many relationships) and {\$Up} (the parent object).

    [field]: '{$i} is the current index of the object'
    [field]: '{$Up.Title} is the parent's title'
    [field]: '{$Up.Up.Title} is the parent's parent's title'

## Configuration

#### Default ignores

Allows you to ignore a field for a class and all it's sub classes

	Seeder:
		default_ignores:
			SiteTree:
				- HasBrokenFile
				- HasBrokenLink

Unless otherwise specified the seeder would not generate a value for 'HasBrokenFile' or 'HasBrokenLink' SiteTree and subclass of SiteTree

#### Default values

Allows you to set a default value for a class and all it's sub classes

	Seeder:
		default_values:
			SiteTree:
				ShowInMenus: 0
				ShowInSearch: 1
				CanViewType: anyone
			Page:
				PreviewImage: 'image(400,300)'

##### Default providers

Allows you to set the default provider for a class and all it's sub classes

	Seeder:
		default_providers:
			Image: ImageProvider

Unless otherwise specified an image will default to using an ImageProvider


## Creating a Provider

Coming soon
