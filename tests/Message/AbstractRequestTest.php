<?php

namespace Omnipay\Moip;


use Mockery;
use Omnipay\Tests\TestCase;

class AbstractRequestTest extends TestCase
{

    public function setUp()
    {
        $this->request = Mockery::mock('\Omnipay\Moip\Message\AbstractRequest')->makePartial();
        $this->request->initialize();
    }

    public function testGetEndpoint()
    {
        $this->request->setTestMode(true);

        $this->assertStringStartsWith('https://desenvolvedor.moip.com.br/sandbox', $this->request->getEndpoint());
    }

    public function testGetEndpointOnLiveMode()
    {
        $this->request->setTestMode(false);

        $this->assertStringStartsWith('https://www.moip.com.br', $this->request->getEndpoint());
    }

    public function testShouldReturnPostOnGetHttpMethod()
    {
        $this->assertEquals('POST', $this->request->getHttpMethod());
    }


}
