# SMS wrapper class for ASPSMS

[![Latest Stable Version](https://poser.pugx.org/nadar/aspsms/v/stable)](https://packagist.org/packages/nadar/aspsms)
[![Total Downloads](https://poser.pugx.org/nadar/aspsms/downloads)](https://packagist.org/packages/nadar/aspsms)

A very simple to use sms sending class for the [aspsms.com](http://aspsms.com) gateway by [indielab](http://www.indielab.ch).

## Installation

The recommended way to install is through [Composer](http://getcomposer.org):

```sh
composer require nadar/aspsms
```

## Usage

```php
<?php

use Aspsms\Aspsms;

// create object class with originator option
$aspsms = new Aspsms('<YOUR_KEY>', '<YOUR_PASSWORD>', array(
    'Originator' => '<MY_SENDER_NAME>'
));

// set message and recipients with tracking their individual tracking numbers.
// attention: verify your tracking numbers first with $aspsms->verifyTrackingNumber(..);
$send = $aspsms->sendTextSms('<YOUR_SMS_MESSAGE>', array(
    '<TRACKING_NR1>' => '<MOBILE_PHONE_NR1>',
    '<TRACKING_NR2>' => '<MOBILE_PHONE_NR2>',
    '<TRACKING_NR3>' => '<MOBILE_PHONE_NR3>'
));

// the message was rejected by aspsms or your authentication credentials where wrong.
if (!$send) {
    echo "[ASPSMS] Error while sending text message: " . $aspsms->getSendStatus();
}

// aspsms takes a little time to delivery your message. You can also send the message and
// store the tracking numbers in a database, so you could retrieve the delivery status later.
sleep(10);

// get deliver status response
$status1 = $aspsms->deliveryStatus('<TRACKING_NR1>');
$status2 = $aspsms->deliveryStatus('<TRACKING_NR2>');
$status3 = $aspsms->deliveryStatus('<TRACKING_NR3>');

var_dump($status1, $status2, $status3);
```

## Contributing

#### Quick guide:

+ Fork the repo.
+ Install dependencies: `composer install`.
+ Make changes.
+ If you are adding functionality or fixing a bug - Please add a unit test!
+ Ensure coding standards.

#### Unit Tests

In order to run the test suite, install the development dependencies:

```sh
composer install
```

Rename the `phpunit.xml.dist` file to `phpunit.xml`, then uncomment the following lines and add your const values:

```xml
<php>
    <!--<const name="USER_KEY" value="" />-->
    <!--<const name="USER_PASS" value="" />-->
    <!--<const name="SMS_NUMBER" value="" />-->
    <!--<const name="SMS_TRACKING" value="" />-->
</php>
```

Test your code with the following command:

```sh
./vendor/bin/phpunit
```

Run the coding standard fixer before send a new pull request.

```sh
./vendor/bin/php-cs-fixer fix src/
```

You're done. Thanks!
