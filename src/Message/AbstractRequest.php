<?php namespace Omnipay\Moip\Message;

use Exception;
//use Moip\Moip;
//use Moip\Auth\BasicAuth;

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


    protected $liveEndpoint = 'https://api.moip.com.br';
    protected $testEndpoint = 'https://sandbox.moip.com.br';  //https://sandbox-tls.moip.com.br (v1)
    protected $version = 2;
    protected $requestMethod = 'POST';
    protected $resource = '';

    
    public function setToken($token)
    {
        $this->setParameter('token', $token);
    }

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

    public function getData()
    {
        $this->validate('token', 'apiKey');

        return [
        ];
    }

    /**
     * @param mixed $data
     *
     * @return \Omnipay\Common\Message\ResponseInterface|\Omnipay\Moip\Message\Response
     */
    /*public function sendData($data)
    {
        $moip = new Moip(new BasicAuth($this->getToken(), $this->getApiKey()), $this->getEndpoint());
    }*/

    public function sendData($data)
    {
        $this->validate('authorization');
        $method = $this->requestMethod;
        $url = $this->getEndpoint();

        $headers = [
            'Content-Type' => 'application/json',
            //'Authorization' => 'Basic '.$this->encodeCredentials($this->getToken(), $this->getApiKey()),
            'Authorization' => $this->getAuthorization(),
        ];

        //print_r([$method, $url, $headers, json_encode($data)]);exit();
        $response = $this->httpClient->request(
            $method,
            $url,
            $headers,
            $this->toJSON($data)
            //http_build_query($data, '', '&')
        );
        //print_r($response);
        //print_r($data);

        if ($response->getStatusCode() != 200 && $response->getStatusCode() != 201 && $response->getStatusCode() != 400) {
            $array = [
                'error' => [
                    'code' => $response->getStatusCode(),
                    'message' => $response->getReasonPhrase()
                ]
            ];

            return $this->response = $this->createResponse($array);
        }

        $json = $response->getBody()->getContents();
        $array = @json_decode($json, true);
        //print_r($array);

        return $this->response = $this->createResponse(@$array);
    }

    protected function setBaseEndpoint($value)
    {
        $this->baseEndpoint = $value;
    }

    protected function setRequestMethod($value)
    {
        return $this->requestMethod = $value;
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

    public function encodeCredentials($token, $appKey)
    {
        return base64_encode($token . ':' . $appKey);
    }

    public function setSoftDescriptor($value)
    {
        return $this->setParameter('soft_descriptor', $value);
    }
    public function getSoftDescriptor()
    {
        return $this->getParameter('soft_descriptor');
    }

    public function setFingerPrint($value)
    {
        return $this->setParameter('fingerPrint', $value);
    }
    public function getFingerPrint()
    {
        return $this->getParameter('fingerPrint');
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

    public function __get($name)
    {
        return $this->getParameter($name);
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

    public function getShippingPrice()
    {
        return $this->getParameter("shipping_price");
    }

    public function setShippingPrice($value)
    {
        return $this->setParameter("shipping_price", $value);
    }

    public function getPaymentType()
    {
        return $this->getParameter('paymentType');
    }

    public function setPaymentType($value)
    {
        $this->setParameter('paymentType', $value);
    }

    

    public function toJSON($data, $options = 0)
    {
        if (version_compare(phpversion(), '5.4.0', '>=') === true) {
            return json_encode($data, $options | 64);
        }
        return str_replace('\\/', '/', json_encode($data, $options));
    }

    public function getTransactionID()
    {
        return $this->getParameter('transactionId');
    }

    public function setTransactionID($value)
    {
        return $this->setParameter('transactionId', $value);
    }

    /**
     * Set client Id
     *
     * @param string $ownId
     */
    public function setCustomerOwnId($ownId)
    {
        $this->setParameter('customerOwnId', $ownId);
    }

    /**
     * Get client Id
     *
     * @return string $ownId
     */
    public function getCustomerOwnId()
    {
        return $this->getParameter('customerOwnId');
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

    public function getResource()
    {
        return $this->resource;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getMethod()
    {
        return $this->requestMethod;
    }

    protected function createResponse($data)
    {
        return $this->response = new Response($this, $data);
    }

    protected function getEndpoint()
    {
        $version = $this->getVersion();
        $endPoint = ($this->getTestMode()?$this->testEndpoint:$this->liveEndpoint);
        return  "{$endPoint}/v{$version}/{$this->getResource()}";
    }

    public function getDataCreditCard()
    {
        $this->validate('card', 'customer', 'fingerPrint');
        $card = $this->getCard();
        $card->validate('number_token');
        $customer = $this->getCustomer();
        $card->validate();

        $data = [
            "installmentCount"=>$this->getInstallments(),
            "statementDescriptor"=>$this->getSoftDescriptor(),
            "fundingInstrument"=>[
                "method"=>"CREDIT_CARD",
                "creditCard"=>[
                    "hash"=> $card->getNumberToken(),
                    "store"=>false,
                    "holder"=>[
                        "fullname"=>$card->getName(),
                        "birthdate"=>$card->getBirthday(),
                        "taxDocument"=>[
                            "type"=>"CPF",
                            "number"=>$card->getHolderDocumentNumber()
                        ],
                        "phone"=>[
                            "countryCode"=>"55",
                            "areaCode"=>substr($card->getPhone(), 0, 2),
                            "number"=>substr($card->getPhone(), 2)
                        ],
                        "billingAddress"=>[
                            "city"=>$card->getShippingCity(),
                            "district"=>$card->getShippingDistrict(),
                            "street"=>$card->getShippingAddress1(),
                            "streetNumber"=>$card->getShippingNumber(),
                            "zipCode"=>$card->getShippingPostcode(),
                            "state"=>$card->getShippingState(),
                            "country"=>$card->getShippingCountry()
                        ]
                    ]
                ]
            ],
            "device"=>[
                "ip"=>$this->getClientIp(),
                /*"geolocation"=>[
                    "latitude"=>-33.867,
                    "longitude"=>151.206
                ],*/
                "userAgent"=>@$_SERVER['HTTP_USER_AGENT']?@$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7) Gecko/20040803 Firefox/0.9.3",
                "fingerprint"=>$this->getFingerPrint()
            ]
        ];

        /*  só é permitido enviar dados do cartão se houve certificação PCI
        if($this->getCardHash()) {
            $data['fundingInstrument']['creditCard']['hash'] = $this->getCardHash();
        } else {
            $data['fundingInstrument']['creditCard']['number'] = $card->getNumber();
            $data['fundingInstrument']['creditCard']['expirationMonth'] = $card->getExpiryMonth();
            $data['fundingInstrument']['creditCard']['expirationYear'] = $card->getExpiryYear();
            //$data['fundingInstrument']['creditCard']['installments'] = $this->getInstallments();
            if ($card->getCvv()) {
                $data['fundingInstrument']['creditCard']['cvc'] = $card->getCvv();
            }
        }*/

        return $data;
    }

    public function getDataBoleto()
    {
        $this->validate('dueDate', 'customer');
        $data = [
            "statementDescriptor"=>$this->getSoftDescriptor(),
            "fundingInstrument"=>[
                "method"=>"BOLETO",
                "boleto"=>[
                    "expirationDate"=>$this->getDueDate(),
                    "instructionLines"=>[
                        "first"=>"Não receber após o vencimento",
                        "second"=>"",
                        "third"=>""
                    ],
                    "logoUri"=>"http://"
                ]
            ]
        ];

        return $data;
    }

    public function getDataPix()
    {
        $data = [];
        return $data;
    }

    public function setOwnId($ownId)
    {
        $this->setParameter('ownId', $ownId);
    }

    public function getOwnId()
    {
        return $this->getParameter('ownId');
    }

    public function setOrderOwnId($ownId)
    {
        $this->setParameter('orderOwnId', $ownId);
    }

    public function getOrderOwnId()
    {
        return $this->getParameter('orderOwnId');
    }

    public function getClientIp()
    {
        $ip = $this->getParameter('clientIp');
        if($ip)
            return $ip;

        $ip = "127.0.0.1";
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            //ip from share internet
            $ip = @$_SERVER['HTTP_CLIENT_IP'];
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            //ip pass from proxy
            $ip = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED'])){
            //ip pass from proxy
            $ip = @$_SERVER['HTTP_X_FORWARDED'];
        }elseif(!empty($_SERVER['HTTP_FORWARDED'])){
            //ip pass from proxy
            $ip = @$_SERVER['HTTP_FORWARDED'];
        }elseif(!empty($_SERVER['REMOTE_ADDR'])){
            $ip = @$_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    public function getCustomer()
    {
        return $this->getParameter('customer');
    }

    public function setCustomer($value)
    {
        return $this->setParameter('customer', $value);
    }

    public function getItemData()
    {
        $data = [];
        $items = $this->getItems();

        if ($items) {
            foreach ($items as $n => $item) {
                $item_array = [];
                $item_array['id'] = $n+1;
                $item_array['title'] = $item->getName();
                $item_array['description'] = $item->getName();
                //$item_array['category_id'] = $item->getCategoryId();
                $item_array['quantity'] = (int)$item->getQuantity();
                //$item_array['currency_id'] = $this->getCurrency();
                $item_array['unit_price'] = (double)($this->formatCurrency($item->getPrice()));

                array_push($data, $item_array);
            }
        }

        return $data;
    }

    //https://paragonie.com/blog/2018/04/protecting-rsa-based-protocols-against-adaptive-chosen-ciphertext-attacks#rsa-doing-it-right
    //https://www.phpclasses.org/package/9206-PHP-Generate-RSA-keys-and-encrypt-data-using-OpenSSL.html
    //https://ebckurera.wordpress.com/2017/06/22/rsa-cryptography-in-php-how-to/
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
}
