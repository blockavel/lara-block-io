# blockavel/lara-block-io

A Laravel package/facade for the Block.io PHP API.

This repository implements a simple ServiceProvider that makes a singleton instance of the Block.io client easily accessible via a Facade in Laravel 5. 

See @BlockIo/block_io-php for more information about the PHP library for Block.io and itsinterfaces.

## Installation using [Composer](https://getcomposer.org)

In your terminal application move to the root directory of your laravel project using the cd command and require theproject as a dependency using composer.

$ composer require blockavel/lara-block-io

This will add the following lines to your composer.json and download the project and its dependencies to your projects ./vendor directory:

```javascript
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
```

## Usage

In order to use the static interface we must customize the application configuration to tell the system where it can find the new service. Open the file config/app.php and add the following lines ([a], [b]):

```php

// config/app.php

return [

    // ...

    'providers' => [

        // ...

        /*
         * Package Service Providers...
         */
        Blockavel\LaraBlockIo\LaraBlockIoServiceProvider::class, // [a]

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

    ],

    // ...

    'aliases' => [

        'App' => Illuminate\Support\Facades\App::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,

        // ...

        'LaraBlockIo' => 'Blockavel\LaraBlockIo\LaraBlockIoFacade', // [b]
        'Hash' => Illuminate\Support\Facades\Hash::class,

        // ...
    ],

];


```

## Publish Vendor

lara-block-io requires a connection configuration. To get started, you'll need to publish all vendor assets running:

php artisan vendor:publish

This will create a config/larablockio.php file in your app that you can modify to set your configuration. Also, make sure you check for changes compared to the original config file after an upgrade.

## Quick Start

Now you should be able to use the facade within your application. Laravel will autoload the corresponding classes once you use the registered alias.

```php

class BlockIoTest{
    
    /**
     * @returns object BlockIo object.
     */
     
    public function test()
    {
        return LaraBlockIo::getBlockIo();
    }
}

```
