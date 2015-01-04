<?php

namespace Aspsms\Test;

use Aspsms\Aspsms;
use Aspsms\Exception;

class AspsmsErrorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Aspsms
     */
    public $aspsms = null;

    /**
     * @var Aspsms
     */
    public $aspsmsValid = null;

    public function setUp()
    {
        $this->aspsms = new Aspsms("NO_KEY", "NO_PASS", array("Originator" => "indielab.ch"));
        $this->aspsmsValid = new Aspsms(USER_KEY, USER_PASS, array("Originator" => "indielab.ch"));
    }

    public function testSendTextSms()
    {
        $sendSms = $this->aspsms->sendTextSms("example", array(
            SMS_TRACKING => SMS_NUMBER,
        ));
        $this->assertEquals(false, $sendSms);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Authorization failed (wrong userkey and/or password).
     */
    public function testExceptionDeliveryStatus()
    {
        $this->aspsms->deliveryStatus("UNKNOWN_TRACKING_CODE");
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage The provided Tracking Number does not exists.
     */
    public function testFailedDeliveryStatus()
    {
        $this->aspsmsValid->deliveryStatus("UNKNOWN_TRACKING_CODE");
    }
}
