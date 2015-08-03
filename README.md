# silverstripe-seeder

Configured through normal silverstripe config files. Sensible defaults, will fill in most fields with relevant data

    ---
    Only:
      environment: 'dev'
    ---
    
    Seeder:
        DataObjects:
            YourDataObjectName:
                count: int (optional) # number of records to generate
                ignore: (optional)
                    - Some Field # ignores this feeder when populating object
                properties: (optional)
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
                        hieght: int
                        max_width: int
                        min_width: int # set in range of min_width < width < max_width
                        max_height: int
                        min_height: int # set in range of min_height < height < max_height
                        
                        // has_one relationship
                        use: new|existing // will either select a random instance or create a new one
                        properties: // same as above (recursive)
                        
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

#Field Types

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

#Usage

To seed database run

    framework/sake DatabaseSeed
    
To unseed database run

    framework/sake DatabaseUnseed
