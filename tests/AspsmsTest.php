<?php

require '../vendor/autoload.php';
require '../aspsms/Aspsms.php';

use Aspsms\Aspsms;

class AspsmsTest extends PHPUnit_Framework_TestCase {
	
	public function testEquals() {
		$aspsms = new Aspsms(ASPSMS_KEY, ASPSMS_PASSWORD, array("Originator" => ASPSMS_ORIGINATOR));
		
		var_dump(count($aspsms));
	}
	
}
