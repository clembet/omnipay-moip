<?php namespace Omnipay\Moip\Message;

class CaptureRequest extends AbstractRequest
{
    protected $resource = 'payments';
    protected $requestMethod = 'POST';


    public function getData()
    {
        $this->validate('transactionId');
        //$data = parent::getData();

        $data = [
            //"amount"=>$this->getAmountInteger()
        ];

        return $data;
    }

    public function sendData($data)
    {
        $this->validate('transactionId');

        $method = $this->requestMethod;
        $url = sprintf(
            "%s/%s/capture",
            $this->getEndpoint(),
            $this->getTransactionID()
        );

        $headers = [
            'Content-Type' => 'application/json',
            //'Authorization' => 'Basic '.$this->encodeCredentials($this->getToken(), $this->getApiKey()),
            'Authorization' => $this->getAuthorization(),
        ];

        //print_r([$method, $url, $headers, json_encode($data)]);//exit();
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
}
