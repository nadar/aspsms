<?php
/*
 * wget https://phar.phpunit.de/phpunit.phar
 * chmod +x phpunit.phar
 * sudo mv phpunit.phar /usr/local/bin/phpunit
 * phpunit --version
 */
require '../vendor/autoload.php';

/**
 * include settings files, should look like this:
 * define('USER_KEY', '<YOUR_KEY>');
 * define('USER_PASS', '<YOUR_PASS>');
 * define('SMS_NUMBER', '<YOUR_PHONE_NUMBER>');
 * define('SMS_TRACKING', '<JUST_A_RANDOM_TRACKING_NUMBER>+time()');
 */
require '../local/settings.php';

use Aspsms\Aspsms;

class AspsmsSuccessTest extends PHPUnit_Framework_TestCase {
	
	public $aspsms = null;
	
	public function setUp () {
		$this->aspsms = new Aspsms(USER_KEY, USER_PASS, array("Originator" => "indielab.ch"));
	}
	
	public function testSendTextSms() {
		$sendSms = $this->aspsms->sendTextSms("example", array(
			SMS_TRACKING => SMS_NUMBER	
		));
		
		$this->assertEquals(TRUE, $sendSms);
	}
	
	public function testVerifyTrackingNumber () {
		$value = "123456789abcedefghijklmnopqrstuv";
		
		$this->assertEquals($value, $this->aspsms->verifyTrackingNumber($value));
		
		$value = "+123456789!?abcdefg";
		$this->assertEquals("123456789abcdefg", $this->aspsms->verifyTrackingNumber($value));
	}
	
	public function testVerifyMobileNumber () {
		$value = "0123456789";
		$this->assertEquals($value, $this->aspsms->verifyMobileNumber($value));
		
		$value = "+0123456789abcdefgh!?";
		$this->assertEquals("0123456789", $this->aspsms->verifyMobileNumber($value));
	}
	
	public function testDeliveryStatus () {
		sleep(5); // wating for aspsms to proceed the delivery
		$response = $this->aspsms->deliveryStatus(SMS_TRACKING);
		/* test does only work for multiple response codes */
		/*
		$this->assertArrayHasKey(SMS_TRACKING, $response);
		$array = $response[SMS_TRACKING];
		*/
		$array = $response;
		$this->assertArrayHasKey('transactionReferenceNumber', $array);
		$this->assertArrayHasKey('deliveryStatus', $array);
		$this->assertArrayHasKey('transactionReferenceNumber', $array);
		$this->assertArrayHasKey('deliveryStatusBool', $array);
		$this->assertArrayHasKey('submissionDate', $array);
		$this->assertArrayHasKey('notificationDate', $array);
		$this->assertArrayHasKey('reasoncode', $array);
	}
}