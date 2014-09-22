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

class AspsmsErrorTest extends PHPUnit_Framework_TestCase {
	
	public $aspsms = null;
	public $aspsmsValid = null;
	
	public function setUp () {
		$this->aspsms = new Aspsms("NO_KEY", "NO_PASS", array("Originator" => "indielab.ch"));
		$this->aspsmsValid = new Aspsms(USER_KEY, USER_PASS, array("Originator" => "indielab.ch"));
	}
	
	public function testSendTextSms() {
		$sendSms = $this->aspsms->sendTextSms("example", array(
			SMS_TRACKING => SMS_NUMBER	
		));
		// false response
		$this->assertEquals(FALSE, $sendSms);
	}
	
	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage Authorization failed (wrong userkey and/or password).
	 */
	public function testExceptionDeliveryStatus () {
		$response = $this->aspsms->deliveryStatus("UNKNOWN_TRACKING_CODE");
	}
	
	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage The provided Tracking Number does not exists.
	 */
	public function testFailedDeliveryStatus () {
		$response = $this->aspsmsValid->deliveryStatus("UNKNOWN_TRACKING_CODE");
	}
	
}