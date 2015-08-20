# silverstripe-seeder

Configured through normal silverstripe config files. Sensible defaults, will fill in most fields with relevant data

    ---
    Only:
      environment: 'dev'
    ---
    
    Seeder:
        DataObjects:
            YourDataObjectName:
                count: int (optional, default=10) # number of records to generate
                publish: true|false (optional, default = true) # page only
                parent: string (optional, className of parent) # page only
                ignore: (optional)
                    - Some Field # ignores this feeder when populating object
                properties: (optional)
                    FieldName: value (predefined value)
                    or
                    FieldName:
                        type: [Field Type see below] (optional)
                        faker_type: string # property for https://github.com/fzaninotto/Faker e.g randomDigit, word
                        nullable: true|false # random chance of setting as null
                        
                        // text
                        min_length: int # min length of string/number of paragraphs
                        max_length: int # max length of string/number of paragraphs
                        length: int  # length of string/number of paragraphs
                        
                        // image
                        width: int 
                        height: int
                        max_width: int
                        min_width: int # set in range of min_width < width < max_width
                        max_height: int
                        min_height: int # set in range of min_height < height < max_height
                        
                        // has_one, many_many relationship
                        use: new|existing # will either select a random instance or create a new one
                        properties: # same as above (recursive)
                        
                        // many_many relationship
                        count: int (optional)
                        min_count: int (optional)
                        max_count: int (optional)
                        
    YourDataObjectName: # this is required
        extensions:
            SeederExtension
            
            
for example

    ---
    Only:
      environment: 'dev'
    ---
    
    Seeder:
        DataObjects:
            TeamMember:
                count: 20
                properties:
                    Mobile:
                        nullable: true
                    Image:
                        max_width: 300 
                        min_width: 250
                        max_height: 160
                        min_height: 140
                        nullable: true

would create 20 team members, some would have Mobile = null, some would have Image = null

# Field Types

possible field types are

    - firstName
    - lastName
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
    - countryCode
    - lat
    - lng
    - link
    - facebookLink
    - googleplusLink
    - twitterLink
    - linkedinLink
    - sort (always set to 0)

# Usage

To seed database run

    framework/sake DatabaseSeed
    
To unseed database run

    framework/sake DatabaseUnseed

# Relationships

## has_one

Explicit support for has_one relationships, can specify properties for a has_one relationship like any other Object

    Seeder:
        HasOneParentObject:
            properties:
                HasOneName:
                    use: existing|new (required)
                    count: int (optional)
                    properties:
                        (as with any other data object)
                        
If "use: new" make sure you add the SeederExtension to has_one Object


## has_many

Implicit support for has_many relationships by setting the has_one of the HasManyObject to use: existing


## many_many

Explicit support for many_many relationships, can specify properties for a many_many relationship like any other Object

    Seeder:
        ManyManyParentObject:
            properties:
                ManyManyName:
                    use: existing|new (required)
                    count: int (optional) default = 2
                    min_count: int (optional, required if max is specified)
                    max_count: int (optional, required if min is specified)
                    properties:
                        (as with any other data object)

If "use: new" make sure you add the SeederExtension to has_one Object


## many_many_extraFields

Not supported
