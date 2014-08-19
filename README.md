# Omnipay: MobilPay

**MobilPay driver for the Omnipay PHP payment processing library**

[Omnipay](https://github.com/omnipay/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.4+. This package implements [MOBILPAY](http://www.mobilpay.ro) support for Omnipay.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "business-mastery/omnipay-mobilpay": "~1.0"
    }
}
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## Basic Usage

The following gateways are provided by this package:

* MobilPay

**Initiating payment request**

```php
$gateway = Omnipay::create('MobilPay');
$gateway->setMerchantId('1234-5678-9012-3456-7890');
$gateway->setPublicKey('/path/to/public.cer');

$response = $gateway->purchase([
    'amount'     => '10.00',
    'currency'   => 'RON',
    'orderId'    => 1,
    'confirmUrl' => 'http://example.com/ipn',
    'returnUrl'  => 'http://www.google.com',
    'details'    => 'Test payment',
    'testMode'   => true,
    'params'     => [
        'selected_package' => 1
    ]
])->send();

$response->redirect();
```

**Processing IPN requests**

```php
$gateway = Omnipay::create('MobilPay');
$gateway->privateKeyPath('/path/to/private.key');

$response = $gateway->completePurchase($_POST)->send();
$response->sendResponse();

switch($response->getMessage())
{
    case 'confirmed_pending': // transaction is pending review. After this is done, a new IPN request will be sent with either confirmation or cancellation

        //update DB, SET status = "pending"

        break;
    case 'paid_pending': // transaction is pending review. After this is done, a new IPN request will be sent with either confirmation or cancellation

        //update DB, SET status = "pending"

        break;
    case 'paid': // transaction is pending authorization. After this is done, a new IPN request will be sent with either confirmation or cancellation

        //update DB, SET status = "open/preauthorized"

        break;
    case 'confirmed': // transaction is finalized, the money have been captured from the customer's account

        //update DB, SET status = "confirmed/captured"

        break;
    case 'canceled': // transaction is canceled

        //update DB, SET status = "canceled"

        break;
    case 'credit': // transaction has been refunded

        //update DB, SET status = "refunded"

        break;
}

```

For general usage instructions, please see the main [Omnipay](https://github.com/omnipay/omnipay)
repository.

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/BusinessMastery/omnipay-mobilpay/issues),
or better yet, fork the library and submit a pull request.
