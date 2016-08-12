<?php

namespace Aspsms\Test;

use Aspsms\Aspsms;

class AspsmsSuccessTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Aspsms
     */
    public $aspsms = null;

    public function setUp()
    {
        $this->aspsms = new Aspsms(USER_KEY, USER_PASS, array("Originator" => "indielab.ch"));
    }

    public function testSendTextSms()
    {
        $sendSms = $this->aspsms->sendTextSms("example", array(
            SMS_TRACKING => SMS_NUMBER,
        ));

        $this->assertEquals(true, $sendSms);
    }
    
    public function testSendLongTextSms()
    {
        // text length: 212
         $sendSms = $this->aspsms->sendTextSms("Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.", array(
            SMS_TRACKING => SMS_NUMBER,
        ));

        $this->assertEquals(true, $sendSms);
    }

    public function testVerifyTrackingNumber()
    {
        $value = "123456789abcedefghijklmnopqrstuv";

        $this->assertEquals($value, $this->aspsms->verifyTrackingNumber($value));

        $value = "+123456789!?abcdefg";
        $this->assertEquals("123456789abcdefg", $this->aspsms->verifyTrackingNumber($value));
    }

    public function testVerifyMobileNumber()
    {
        $value = "0123456789";
        $this->assertEquals($value, $this->aspsms->verifyMobileNumber($value));

        $value = "+0123456789abcdefgh!?";
        $this->assertEquals("0123456789", $this->aspsms->verifyMobileNumber($value));
    }

    public function testImmediateDeliveryStatus()
    {
        // get the response
        $response = $this->aspsms->deliveryStatus(SMS_TRACKING);
        // see if multiple tracking codes exist.
        if (array_key_exists(SMS_TRACKING, $response)) {
            $array = $response[SMS_TRACKING];
        } else {
            $array = $response;
        }

        $this->assertArrayHasKey('transactionReferenceNumber', $array);
        $this->assertArrayHasKey('deliveryStatus', $array);
        $this->assertArrayHasKey('deliveryStatusBool', $array);
    }
    
    public function testDeliveryStatus()
    {
        // waiting for aspsms to proceed the delivery
        sleep(5);
        // get the response
        $response = $this->aspsms->deliveryStatus(SMS_TRACKING);
        // see if multiple tracking codes exist.
        if (array_key_exists(SMS_TRACKING, $response)) {
            $array = $response[SMS_TRACKING];
        } else {
            $array = $response;
        }

        $this->assertArrayHasKey('transactionReferenceNumber', $array);
        $this->assertArrayHasKey('deliveryStatus', $array);
        $this->assertArrayHasKey('transactionReferenceNumber', $array);
        $this->assertArrayHasKey('deliveryStatusBool', $array);
        $this->assertArrayHasKey('submissionDate', $array);
        $this->assertArrayHasKey('notificationDate', $array);
        $this->assertArrayHasKey('reasoncode', $array);
    }
}
