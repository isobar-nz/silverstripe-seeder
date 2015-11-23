SilverStripe Seeder
===================

Sick of testing pagination by setting page length to 1 and making two data objects? Look no further!

- [Creating providers](http://github.com/Little-Giant/silverstripe-seeder/docs/providers.md)

# Features
-   Declaritive method of generating test data
-   Easy to way to share data dependencies with other developers
-   Easy to extend

# Installation
Installation via composer

``` bash
composer require littlegiant/silverstripe-seeder
```

# How to use

Add to your configuration

``` yaml
Seeder:
    create:
        Page:
            count: 100
            fields:
                Title: 'Hello Seeder! {$i}'
        Member: member(test@test.com,password)
```

Change to project root and run

``` bash
(unix)      $ framework/sake seed flush=1
(windows)   > php framework/cli-script.php seed flush=1
```

## Command line options

``` bash
framework/sake seed [-k|--key KEY] [-c|--class CLASS] [-f|--force] [flush=1|all]
framework/sake unseed [-k|--key KEY] [flush=1|all]
```

| Option | Description |
| -- | -- |
| `--force` | run the seeder ignoring current records |
| `--key` | only (un)seed records matching this key |
| `--class` | only seed records for this root class |
| `flush` | useful silverstripe CliController arg that flushes configuration |


## Providers

Providers are a simple way to customise what data is generated. The seeder comes with a bunch of useful providers

| Provider | Description | Example |
| -- | -- | -- |
| ValueProvider | Use the given value, select variables included | `Field: 'this is the value'` |
| DateProvider | Generate a date | `Field: date(+3 months)` |
| FakerProvider | Generate data using the php faker library | `Field: faker(sentences,3)` |
| FirstObjectProvider | Returns the first instance of the class | `Parent: first(Page)` |
| RandomObjectProvider | Returns a list of random objects for class | `Children: random(Page)` |
| HTMLProvider | Returns random HTML | `Field: html()` |
| ImageProvider | Returns an `Image` of a [placehold.it](http://placehold.it) image | `Image: image(300,400)` |
| MemberProvider | Returns a member with email and password | `Member: member(test@test.com,password)` |

Check here for more information on [creating providers](http://github.com/Little-Giant/silverstripe-seeder/docs/providers.md)

## Example

``` yaml
--
Name: seeder
--

Seeder:
    create:
        HomePage:
            fields:
                Title: Home
                Content: >
                    <p>This is an awesome paragraph that can welcome your visitors</p>
        Blog:
            fields
                Title: Magic in a bottle
        Member: member(admin@mysite.com,default admin password)

--
Name: seeder-dev
Only:
    environment: dev
--

Seeder:
    create:
        Author:
            count: 10
            fields:
                Name: faker(name)
        BlogTag:
            count: 10
        BlogPost:
            count: 100
            fields:
                Parent: first(blog)
                Author: random()
                Title: 'Blog post {$i}'
                Tags: random(BlogTag,3)
```

## License

The MIT License (MIT)

Copyright (c) 2015 Little Giant Design Ltd

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

## Contributing

Pull requests are welcome

### Code guidelines

This project follows the standards defined in:

* [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md)
* [PSR-1](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)
* [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
