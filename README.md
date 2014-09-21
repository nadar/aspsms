SMS wrapper class for ASPSMS.COM service
================

A very simple to use ams sending class for the aspsms.com gateway by indielab.ch.

Installation instructions
-------------------------

1. Register your account on aspsms.com (http://aspsms.com/registration.asp)
2. Download the aspsms-php-class (https://github.com/nadar/aspsms-php-class/archive/master.zip)
3. Copy the example code
4. Fill up your credentials and informations (userkey, password, tracking-numbers and recipients)
5. Happy sending

Example code
------------

	<?php
		// include the aspsms class
		include 'lib/aspsms.class.php';

		// create object class with originator option
		$aspsms = new Aspsms('<YOUR_KEY>', '<YOUR_PASSWORD>', array(
    		'Originator' => '<MY_SENDER_NAME>'
		));

		// set message and recipients with tracking numbers
		// attention: verify your tracking numbers with
		// $aspsms->verifyTrackingNumber(..);
		$send = $aspsms->sendTextSms('<YOUR_SMS_MESSAGE>', array(
    		'<TRACKING_NR1>' => '<MOBILE_PHONE_NR1>',
			'<TRACKING_NR2>' => '<MOBILE_PHONE_NR2>',
    		'<TRACKING_NR3>' => '<MOBILE_PHONE_NR3>'
		));

		// see if something went wrong while sending
		if (!$send) {
    		echo "[ASPSMS] Error while sending text message: " . $aspsms->getSendStatus();
		}

		// to get valid delivery status we need to have a delay so the sms can be processed
		sleep(10);

		// get deliver status response
		$status1 = $aspsms->deliveryStatus('<TRACKING_NR1>');
		$status2 = $aspsms->deliveryStatus('<TRACKING_NR2>');
		$status3 = $aspsms->deliveryStatus('<TRACKING_NR3>');

		var_dump($status1, $status2, $status3);
	?>
