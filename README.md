SMS wrapper class for ASPSMS.COM
================

A very simple to use sms sending class for the aspsms.com gateway by indielab.ch. This is the _new Composer Version_ of this class.

Installation instructions
-------------------------

1. added "nadar/aspsms" : "dev-master" to your composer.json file.
2. Start the Aspsms class with your credentials.
3. Happy sending

Example composer.json
------------

	{
		"require" : {
			"nadar/aspsms": "dev-master"
		}
	}
	
Example ASPSMS class usage
-----------

	<?php
		require '../vendor/autoload.php';
		
		use Aspsms\Aspsms;

		// create object class with originator option
		$aspsms = new Aspsms('<YOUR_KEY>', '<YOUR_PASSWORD>', array(
    		'Originator' => '<MY_SENDER_NAME>'
		));

		// set message and recipients with tracking theyr individual tracking numbers.
		// attention: verify your tracking numbers first with $aspsms->verifyTrackingNumber(..);
		$send = $aspsms->sendTextSms('<YOUR_SMS_MESSAGE>', array(
    		'<TRACKING_NR1>' => '<MOBILE_PHONE_NR1>',
			'<TRACKING_NR2>' => '<MOBILE_PHONE_NR2>',
    		'<TRACKING_NR3>' => '<MOBILE_PHONE_NR3>'
		));

		// the message was rejected by aspsms or your authentification credentials where wrong.
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
	?>

[indielab.ch](www.indielab.ch)
