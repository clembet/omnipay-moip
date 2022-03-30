<?php namespace Omnipay\Moip\Message;

//https://docs.moip.com.br/reference#consultar-pagamento-mp
class FetchTransactionRequest extends AbstractRequest
{
    protected $resource = 'payments';
    protected $requestMethod = 'GET';

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        return parent::getData();
    }

    public function sendData($data)
    {
        $this->validate('transactionId');

        $method = $this->requestMethod;
        $url = sprintf(
            "%s/%s",
            $this->getEndpoint(),
            $this->getTransactionID()
        );

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic '.$this->encodeCredentials($this->getToken(), $this->getApiKey()),
            //'Authorization' => 'OAuth '.$this->getAccessToken(),
        ];

        //print_r([$method, $url, $headers, json_encode($data)]);exit();
        $response = $this->httpClient->request(
            $method,
            $url,
            $headers,
            //$this->toJSON($data)
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

}
