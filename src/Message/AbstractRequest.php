<?php

namespace Omnipay\Moip\Message;

use Moip\Moip;
use Moip\Auth\BasicAuth;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
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

    //https://dev.wirecard.com.br/docs/protocolo-seguranca
    /**
     * Live Endpoint URL
     *
     * @var string URL
     */
    //protected $liveEndpoint = 'https://api.moip.com.br/v2';

    /**
     * Test Endpoint URL
     *
     * @var string URL
     */
    //protected $testEndpoint = 'https://sandbox.moip.com.br/v2';

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

    /**
     * @param mixed $data
     *
     * @return \Omnipay\Common\Message\ResponseInterface|\Omnipay\Moip\Message\Response
     */
    public function sendData($data)
    {
        $moip = new Moip(new BasicAuth($this->getToken(), $this->getApiKey()), $this->getEndpoint());
    }

    /**
     * Verify environment of the service payment and return correct endpoint url
     *
     * @return string
     */
    protected function getEndpoint()
    {
        return $this->getTestMode() ? $this->getTestEndpoint() : $this->getLiveEndpoint();
    }

    /**
     * @param $data
     *
     * @return \Omnipay\Moip\Message\Response
     */
    protected function createResponse($data)
    {
        return $this->response = new Response($this, $data);
    }

    /**
     * Get HTTP Method.
     *
     * This is nearly always POST but can be over-ridden in sub classes.
     *
     * @return string the HTTP method
     */
    protected function getHttpMethod()
    {
        return 'POST';
    }

    /**
     * Return production environment url of service
     *
     * @return string
     */
    private function getLiveEndpoint()
    {
        return Moip::ENDPOINT_PRODUCTION;
    }

    /**
     * Return test environment url of service
     *
     * @return string
     */
    private function getTestEndpoint()
    {
        return Moip::ENDPOINT_SANDBOX;
    }

    /**
     * Get the customer reference.
     *
     * @return string
     */
    public function getCustomerReference()
    {
        return $this->getParameter('customerReference');
    }

    /**
     * Set the customer reference.
     *
     * Used when calling CreateCard on an existing customer.  If this
     * parameter is not set then a new customer is created.
     *
     * @param string $value
     *
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setCustomerReference($value)
    {
        return $this->setParameter('customerReference', $value);
    }

    /**
     * Get the order reference.
     *
     * @return string
     */
    /*public function getOrderReference()
    {
        return $this->getParameter('orderReference');
    }*/
    public function getOrderId()
    {
        return $this->getParameter('order_id');
    }

    /**
     * Set the order reference.
     *
     * @param string $value
     *
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    /*public function setOrderReference($value)
    {
        return $this->setParameter('orderReference', $value);
    }*/
    public function setOrderId($value)
    {
        return $this->setParameter('order_id', $value);
    }

    /**
     * Set card hash
     *
     * @param string $hash
     */
    public function setCardHash($hash)
    {
        $this->setParameter('cardHash', $hash);
    }

    /**
     * Get card hash
     *
     * @return string
     */
    public function getCardHash()
    {
        return $this->getParameter('cardHash');
    }

    /**
     * @param string $value Date format 'yyyy-mm-dd'
     */
    public function setExpirationDate($value)
    {
        $this->setParameter('expirationDate', $value);
    }

    /**
     * @return string
     */
    public function getExpirationDate()
    {
        return $this->getParameter('expirationDate');
    }

    /**
     * @param string $value
     */
    public function setInstructionLinesFirst($value)
    {
        $this->setParameter('instructionLinesFirst', $value);
    }

    /**
     * @return string
     */
    public function getInstructionLinesFirst()
    {
        return $this->getParameter('instructionLinesFirst');
    }

    /**
     * @param string $value
     */
    public function setInstructionLinesSecond($value)
    {
        $this->setParameter('instructionLinesSecond', $value);
    }

    /**
     * @return string
     */
    public function getInstructionLinesSecond()
    {
        return $this->getParameter('instructionLinesSecond');
    }

    /**
     * @param string $value
     */
    public function setInstructionLinesThird($value)
    {
        $this->setParameter('instructionLinesThird', $value);
    }

    /**
     * @return string
     */
    public function getInstructionLinesThird()
    {
        return $this->getParameter('instructionLinesThird');
    }

    /**
     * Get the card data.
     *
     * Because the stripe gateway uses a common format for passing
     * card data to the API, this function can be called to get the
     * data from the associated card object in the format that the
     * API requires.
     *
     * @return array
     * @throws \Omnipay\Common\Exception\InvalidCreditCardException
     */
    protected function getCardData()
    {
        $card = $this->getCard();
        $card->validate();

        $data = [];
        if($this->getCardHash()) {
            $data['hash'] = $this->getCardHash();
        } else {
            $data['number'] = $card->getNumber();
            $data['expirationMonth'] = $card->getExpiryMonth();
            $data['expirationYear'] = $card->getExpiryYear();
            $data['installments'] = $this->getInstallments();
            if ($card->getCvv()) {
                $data['cvc'] = $card->getCvv();
            }
        }

        //$data['shippingAmount'] = $this->getShippingAmount();

        $customer['name'] = $card->getName();
        $customer['firstName'] = $card->getShippingFirstName();
        $customer['lastName'] = $card->getShippingLastName();
        $customer['birthday'] = $card->getBirthday();
        $customer['email'] = $card->getEmail();
        $customer['phone'] = $card->getPhone();
        $customer['doc'] = $card->getHolderDocumentNumber();
        $customer['address_line1'] = $card->getAddress1();
        $customer['address_line2'] = $card->getAddress2();
        $customer['address_number'] = $card->getBillingNumber();
        $customer['address_city'] = $card->getCity();
        $customer['address_district'] = $card->getBillingDistrict();
        $customer['address_zip'] = $card->getPostcode();
        $customer['address_state'] = $card->getState();
        $customer['address_country'] = $card->getCountry();

        $data['customer'] = $customer;

        return $data;
    }

    public function getBoletoData()
    {
        $this->validate('expirationDate', 'instructionLinesFirst');
        $data = [];

        $data['expirationDate'] = $this->getExpirationDate();
        $data['instructionLines'] = [];
        $data['instructionLines']['first'] = $this->getInstructionLinesFirst();
        if($this->getInstructionLinesSecond()) {
            $data['instructionLines']['second'] = $this->getInstructionLinesSecond();
        }
        if($this->getInstructionLinesThird()) {
            $data['instructionLines']['third'] = $this->getInstructionLinesThird();
        }

        return $data;
    }

    protected function decode($data)
    {
        return json_decode($data, true);
    }

    public function getInstallments()
    {
        return $this->getParameter('installments');
    }

    public function setInstallments($value)
    {
        return $this->setParameter('installments', $value);
    }

    public function getShippingAmount()
    {
        return $this->getParameter('shippingAmount');
    }

    public function setShippingAmount($value)
    {
        return $this->setParameter('shippingAmount', $value);
    }

    public function getPaymentType()
    {
        return $this->getParameter('paymentType');
    }

    public function setPaymentType($value)
    {
        $this->setParameter('paymentType', $value);
    }

    public function getDueDate()
    {
        $dueDate = $this->getParameter('dueDate');
        if($dueDate)
            return $dueDate;

        $time = localtime(time());
        $ano = $time[5]+1900;
        $mes = $time[4]+1+1;
        $dia = 1;// $time[3];
        if($mes>12)
        {
            $mes=1;
            ++$ano;
        }

        $dueDate = sprintf("%04d-%02d-%02d", $ano, $mes, $dia);
        $this->setDueDate($dueDate);

        return $dueDate;
    }

    public function setDueDate($value)
    {
        return $this->setParameter('dueDate', $value);
    }

    /**
     * Don't throw exceptions for 4xx errors
     */
    private function addListener4xxErrors()
    {
        $this->httpClient->getEventDispatcher()->addListener(
            'request.error',
            function ($event) {
                if ($event['response']->isClientError()) {
                    $event->stopPropagation();
                }
            }
        );
    }
}
