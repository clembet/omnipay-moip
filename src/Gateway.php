<?php

namespace Omnipay\Moip;

use Exception;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\RequestInterface;

/**
 * @method \Omnipay\Common\Message\RequestInterface completeAuthorize(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface completePurchase(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface refund(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface createCard(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface updateCard(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface deleteCard(array $options = array())
 */
class Gateway extends AbstractGateway
{
    protected $pubKey = '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAjp6zfhT4b7oIfGVW55Lp
YzedLAcSD0DJ5Muk1udi3D1VLCemKcKzL0CkVHOMCwmrygP7nnOXRONEpqK/PGHj
ocoV/YvHjmA4tPe0l77xDjpigJWf2FDDRJuXRuU2mKoM+2rmXrazk5UEJrIbGpIK
J42XEBkVtSaxmD/5cKGnH+icY09Gt9i8ljOys96fjZYEnktaHirwX66gWGyjRZ9Z
N+MbsmjWCeAjqLCvsWvF2jGTbRDkwW7qZtoOLFfCF/DTDRWYrgVX3a9HL+PPec2r
Gp+TKdMhAz4IkqiSXiw6+eYpSAfvevhg7CC7UYeb427wFYAhExFtx+d+JhCA70yM
twIDAQAB
-----END PUBLIC KEY-----';

    public function getDefaultParameters()
    {
        return [
            'token'    => '',
            'apiKey'   => '',
            'testMode' => false,
        ];
    }

    /**
     * Get gateway display name
     *
     * This can be used by carts to get the display name gateway.
     *
     * @return string
     */
    public function getName()
    {
        return 'Moip';
    }

    /**
     * Set token authorization service
     *
     * @param string $token
     */
    public function setToken($token)
    {
        $this->setParameter('token', $token);
    }

    /**
     * Get token authorization service
     *
     * @return string
     */
    public function getToken()
    {
        return $this->getParameter('token');
    }

    /**
     * Set api key authentication service
     *
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->setParameter('apiKey', $apiKey);
    }

    /**
     * Get api key authentication service
     *
     * @return string $apiKey
     */
    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }

    public function setAccessToken($value)
    {
        return $this->setParameter('accessToken', $value);
    }

    public function getAccessToken()
    {
        return $this->getParameter('accessToken');
    }

    public function setAuthorization($value)
    {
        return $this->setParameter('authorization', $value);
    }

    public function getAuthorization()
    {
        return $this->getParameter('authorization');
    }

    /**
     * Set client Id
     *
     * @param string $ownId
     */
    public function setOwnId($ownId)
    {
        $this->setParameter('ownId', $ownId);
    }

    /**
     * Get client Id
     *
     * @return string $ownId
     */
    public function getOwnId()
    {
        return $this->getParameter('ownId');
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function createCustomer($parameters = [])
    {
        return $this->createRequest('\Omnipay\Moip\Message\CreateCustomerRequest', $parameters);
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function createOrder($parameters = [])
    {
        return $this->createRequest('\Omnipay\Moip\Message\CreateOrderRequest', $parameters);
    }

    /**
     * Create request for to consume service
     *
     * @param array $parameters
     *
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function authorize(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\Moip\Message\AuthorizeRequest', $parameters);
    }

    /**
     * Capture Request
     *
     * Use this request to capture and process a previously created authorization.
     *
     * @param array $parameters
     * @return \Omnipay\GetNet\Message\CaptureRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Moip\Message\CaptureRequest', $parameters);
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\Common\Message\AbstractRequest|\Omnipay\Common\Message\RequestInterface
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\Moip\Message\PurchaseRequest', $parameters);
    }

    /**
     * Void Transaction Request
     *
     *
     *
     * @param array $parameters
     * @return \Omnipay\GetNet\Message\VoidRequest
     */
    public function void(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Moip\Message\VoidRequest', $parameters);
    }

    public function fetchTransaction(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\Moip\Message\FetchTransactionRequest', $parameters);
    }

    public function acceptNotification(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\Moip\Message\NotificationRequest', $parameters);
    }

    public function parseResponse($data)
    {
        $request = $this->createRequest('\Omnipay\Moip\Message\PurchaseRequest', []);
        return new \Omnipay\Moip\Message\Response($request, (array)$data);
    }

    public function getAntifraudeScriptUrl()
    {
        //https://cdn.jsdelivr.net/npm/clientjs@0.1.11/dist/client.min.js
        return "https://raw.githubusercontent.com/jackspirou/clientjs/master/dist/client.min.js";
    }

    function rsa_encrypt($data, $pubkey)
    {
        if (openssl_public_encrypt($data, $encrypted, $pubkey))
            $data = base64_encode($encrypted);
        else
            throw new Exception('Unable to encrypt data. Perhaps it is bigger than the key size?');

        return $data;
    }

    function rsa_decrypt($data, $privkey)
    {
        if (openssl_private_decrypt(base64_decode($data), $decrypted, $privkey))
            $data = $decrypted;
        else
            $data = '';

        return $data;
    }

    function getPubKey()
    {
        return $this->pubKey;
    }
}