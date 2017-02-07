<?php

namespace Aspsms\Test;

use Aspsms\Aspsms;

class AspsmsTest extends \PHPUnit_Framework_TestCase
{
    protected $aspsms;
    
    protected function setUp()
    {
        parent::setUp();
        $this->aspsms = new Aspsms('does', 'notmatter');
    }
    public function testParseResponse()
    {
        $this->assertSame([0 => 'value'], $this->aspsms->parseResponse('value'));
        
        $this->expectException('Aspsms\Exception');
        $this->aspsms->parseResponse(null);
    }
    
    public function testDateSplitter()
    {
        $this->assertSame('30.01.2013 22:30:15', $this->aspsms->dateSplitter('30012013223015'));
        
        $this->expectException('Aspsms\Exception');
        $this->aspsms->dateSplitter('123123123');
    }
}
