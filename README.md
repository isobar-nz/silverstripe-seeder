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
                        width: int (image)
                        hieght: int (image)
                        max_width: int (image)
                        min_width: int (image) # set in range of min_width < width < max_width
                        max_height: int (image)
                        min_height: int (image) # set in range of min_height < height < max_height
                        
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
