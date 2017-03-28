## lara-block-io
A Laravel package/facade for the Block.io PHP API.

This repository implements a simple ServiceProvider that makes a singleton instance of the 
Block.io client easily accessible via a Facade in Laravel 5. 

See @BlockIo/block_io-php for more information about the PHP library for Block.io and its
interfaces.

# Installation using [Composer](https://getcomposer.org)

In your terminal application move to the root directory of your laravel project using the cd command and require the 
project as a dependency using composer.

$ composer require blockavel/lara-block-io

This will add the following lines to your composer.json and download the project and its dependencies to your projects ./vendor directory:

// 
./composer.json
{
    "name": "blockavel/lara-block-io",
    "description": "A dummy project used to test the Laravel Block.io Facade.",

    // ...

    "require": {
        "php": ">=5.5.9",
        "laravel/framework": "5.2.*",
        "blockavel/lara-block-io": "1.0.*",
        // ...
    },

    //...
}
