<?php

namespace Omnipay\Moip;

use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    /**
     * @var $gateway Gateway
     */
    protected $gateway;

    public function setUp()
    {
        parent::setUp();

        $this->gateway = new Gateway();
    }

    public function testShouldBeInstanceOfGateway()
    {
        $this->assertInstanceOf('Omnipay\Moip\Gateway', $this->gateway);
    }

    public function testShouldBeReturnGatewayName()
    {
        $this->assertEquals("MoIP Payment", $this->gateway->getName(), "The gateway name do not matter with the 'MoIP Payment'");
    }

    public function testShouldBeReturnGatewayShortName()
    {
        $this->assertEquals("MoIP", $this->gateway->getShortName(), "The gateway name do not matter with the 'MoIP'");
    }

    /**
     * @dataProvider dataProvider
     */
    public function testIfTokenServiceIsSetup($token)
    {

        $this->gateway->setToken($token);
        $this->assertEquals($token, $this->gateway->getToken(), "The token set is not equals same that passed");
    }

    /**
     * @dataProvider dataProvider
     */
    public function testIfTokenServiceIsSetupIsNotTheSameWithWrongToken($token)
    {

        $this->gateway->setToken($token.'salt3@%$#');
        $this->assertNotEquals($token, $this->gateway->getToken(), "The token set is not equals same that passed");
    }

    /**
     * @dataProvider dataProvider
     */
    public function testIfApiKeyServiceIsSetup($apiKey)
    {
        $this->gateway->setApiKey($apiKey);
        $this->assertEquals($apiKey, $this->gateway->getApiKey(), "The api key set is not the same that passed");
    }

    /**
     * @dataProvider dataProvider
     */
    public function testIfApiKeyServiceIsSetupIsNotTheSameWithWrongApiKey($apiKey)
    {
        $this->gateway->setApiKey($apiKey.'salt3@%$#');
        $this->assertNotEquals($apiKey, $this->gateway->getApiKey(), "The api key set is not the same that passed");
    }



    public function dataProvider()
    {
        return array(
            array('ABABABABABABABABABABABABABAB'),
            array('CBCBCBCBCBCBCBCBCBCBCBCBCBCB'),
            array('ACACACACACACACACACACACACACAC'),
        );
    }

    public function testAuthorize()
    {
        $request = $this->gateway->authorize(array('' => ''));
        $this->assertInstanceOf('Omnipay\Moip\Message\AuthorizeRequest', $request);
    }
}