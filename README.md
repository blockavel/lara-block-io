# blockavel/lara-block-io

[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/hyn/multi-tenant/2.x/license.md)
[![Build Status](https://travis-ci.org/blockavel/lara-block-io.svg?branch=master)](https://travis-ci.org/blockavel/lara-block-io)
[![StyleCI](https://styleci.io/repos/83922877/shield?branch=master)](https://styleci.io/repos/83922877)

A Laravel package/facade for the Block.io API PHP wrapper.

This repository implements a simple Service Provider of the Block.io client, and makes it easily accessible via a Facade in Laravel >= 5. 

See [@BlockIo/block_io-php](https://github.com/BlockIo/block_io-php) and the [BlockIo PHP API docs](https://block.io/api/simple/php) for more information about the PHP wrapper of the Block.io API and its interfaces.

## Requirements

Create an account at [Block.io](https://block.io/users/sign_up) and take note of your API key under Account > Dashboard.

The BlockIo library requires the 'mcrypt' (please note that mcrypt has been deprecated for php7.1), 'gmp', and 'cURL' extensions for PHP as well as the 'bcmath' library. To enable these, please see:

-[mCrypt Installation Guide](http://php.net/manual/en/mcrypt.installation.php)

-[GMP Installation Guide](http://php.net/manual/en/gmp.installation.php)

-[cURL Installation Guide](http://php.net/manual/en/curl.installation.php)

-[bcmath Installation Guide](http://php.net/manual/en/book.bc.php)

## Installation using [Composer](https://getcomposer.org)

In your terminal application move to the root directory of your laravel project using the cd command and require the project as a dependency using composer.

composer require blockavel/lara-block-io

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

This will create a config/larablockio.php file in your app that you can modify to set your configuration. Make sure you check for changes compared to the original config file after an upgrade.

Now you should be able to use the facade within your application. Ex:

```php
namespace App;

use Illuminate\Database\Eloquent\Model;

class BlockIoTest extends Model
{
    /**
     * Get the balance information associated with a Bitcoin Dogecoin,
     * or Litecoin account.
     *
     * @return object Contains balance information
     */
     
    public function test()
    {
        return LaraBlockIo::getBalanceInfo();
    }
}

```

## List of Available Methods

### Balance and Network Info 
```php
// BlockIo getter method, returns a BlockIo object.
LaraBlockIo::getBlockIo();
// Get the balance information associated with a Bitcoin Dogecoin, or Litecoin account.
LaraBlockIo::getBalanceInfo();
// Get the Network associated with your API KEY.
LaraBlockIo::getNetwork();  
// Get the balance associated with all your addresses in the selected network.
LaraBlockIo::getAvailableBalance();
// Get the the balance that's pending confirmation in the selected network.
LaraBlockIo::getPendingReceivedBalance();
// Get address(es) balance by specified address(es).
LaraBlockIo::getAddressesBalanceByAddress($addresses);
// Get address(es) balance by specified label(s).
LaraBlockIo::getAddressesBalanceByLabels($labels);
// Get user(s) balance.
LaraBlockIo::getUsersBalance($userIds);
// Get network fee estimate for transacting (withdrawing, sending).
LaraBlockIo::getNetworkFeeEstimate($amounts, $addresses);
```
### Addresses
```php
// Create new address.
LaraBlockIo::createAddress($label); 
// Get all the (unarchived) addresses information.
LaraBlockIo::getAddressesInfo();
// Get all the (unarchived) addresses information without balance.
LaraBlockIo::getAddressesInfoWithoutBalances(); 
// Get the (unarchived) addresses associated with your account.
LaraBlockIo::getAddresses(); 
// Get the (unarchived) addresses associated with your account without balance.
LaraBlockIo::getAddressesWithoutBalances();
// Get address by label.
LaraBlockIo::getAddressByLabel($label);
// Get all the users associated with your account in a given network.
LaraBlockIo::getUsers()
// Get a user's address.
LaraBlockIo::getUserAddress($userId);
```
### Withdraw
```php
// Withdraws amount of coins from any addresses in your account.
LaraBlockIo::withdraw($amounts, $toAddresses, $nonce = null);
// Withdraws amount of coins from specific addresses in your account.
LaraBlockIo::withdrawFromAddressesToAddresses($amounts, $fromAddresses, $toAddresses, $nonce = null);
LaraBlockIo::withdrawFromLabelsToLabels($amounts, $fromLabels, $toLabels, $nonce = null);
LaraBlockIo::withdrawFromLabelsToAddresses($amounts, $fromLabels, $toAddresses, $nonce = null);
```
### Archive
```php
// Archive adress(es).
LaraBlockIo::archiveAddressesByAddress($addresses);
LaraBlockIo::archiveAddressesByLabels($labels);
// Unarchive address(es)
LaraBlockIo::unarchiveAddressesByAddress($addresses);
LaraBlockIo::unarchiveAddressesByLabels($labels);
// Returns all the archived addresses.
LaraBlockIo::getArchivedAddresses();
```
### Transactions
```php
// Returns various data for transactions spent or received.
LaraBlockIo::getTransactionsByAddresses($type, $addresses, $beforeTx = null);
LaraBlockIo::getTransactionsByLabels($type, $labels, $beforeTx = null);
LaraBlockIo::getTransactionsByUserIds($type, $userIds, $beforeTx = null);
LaraBlockIo::getReceivedTransactions($beforeTx = null);
LaraBlockIo::getSentTransactions($beforeTx = null);
// Returns the prices from the largest exchanges for the given network.
LaraBlockIo::getCurrentPrice($baseCurrency = null);
// Returns an array of transactions that were sent by Block.io Green Addresses.
LaraBlockIo::isGreenTransaction($txIds);
// Get pending transactions.
LaraBlockIo::getNotConfirmedTxs($toAddress, $confidenceThreshold);
```
### DTrust
```php
// Get all dtrust addresses.
LaraBlockIo::getDTrustAddresses();
// Create a MultiSig address.
LaraBlockIo::createMultiSigAddress($label, $reqSigs, $s1, $s2, $s3 = null, $s4 = null);
// Get details of a dtrust address associated with a given label.
LaraBlockIo::getDTrustInfoByLabel($label);
// Perform a MultiSig withdraw.
LaraBlockIo::multiSigWithdraw($label, $toAddresses, $amount);
// Returns a MultiSig withdraw object for signing.
LaraBlockIo::getMultiSigWithdraw($referenceId);
// Sign MultiSig withdraw.
LaraBlockIo::signMultiSigWithdraw($reference_id, $passphrase);
// Returns sent dtrust transactions.
LaraBlockIo::getSentDTrustTransactions($beforeTx = null);
// Returns received dtrust transactions.
LaraBlockIo::getReceivedDTrustTransactions($beforeTx = null);
// Returns information associated with dtrust transactions.
LaraBlockIo::getDtrustTransactionsByAddresses($type, $addresses, $beforeTx = null);
LaraBlockIo::getDtrustTransactionsByLabels($type, $labels, $beforeTx = null);
LaraBlockIo::getDTrustTransactionsByUserIds($type, $userIds, $beforeTx = null);
// Get balance associated with dtrust addresses.
LaraBlockIo::getDTrustAddressBalance($addresses);
// Archive dtrust addresses.
LaraBlockIo::archiveDTrustAddress($addresses);
// Unarchive dtrust addresses.
LaraBlockIo::unarchiveDTrustAddress($addresses);
// Get archived addresses.
LaraBlockIo::getArchivedDTrustAddresses();
// Get estimated network fee for dtrust transactions.
LaraBlockIo::getNetworkDTrustFeeEstimate($amounts, $fromAddress, $toAddress);
```
### Sweep Funds
```php
// Sweep funds from external address to a BlockIo address.
LaraBlockIo::sweepFromAddress($fromAddress, $toAddress, $privateKey);
```
## Testing

Unit Tests are created with PHPunit and orchestra/testbench, they can be ran with ./vendor/bin/phpunit.

## Contributing

Find an area you can help with and do it. Open source is about collaboration and open participation. 
Try to make your code look like what already exists or better and submit a pull request. Also, if
you have any ideas on how to make the code better or on improving the scope and functionality please
contact any of the contributors.

## License

MIT License.
