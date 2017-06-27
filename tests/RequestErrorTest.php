<?php

namespace Aspsms\Test;

class RequestErrorTest extends \PHPUnit_Framework_TestCase
{
    protected $request;
    
    protected $badSSLUrl = "https://webservice.aspsms.com/aspsmsx2.asmx/";

    public function setUp()
    {
        parent::setUp();
        $this->request = new \Aspsms\Request($this->badSSLUrl);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessageRegExp /Invalid API Response .*SSL.?/
     */
    public function testInvalidSSL()
    {
        $this->request->transfer();
    }
}
