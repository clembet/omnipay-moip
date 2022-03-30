<?php namespace Omnipay\Moip\Message;


use Omnipay\Common\Message\AbstractResponse;

class Response extends AbstractResponse
{
    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        if(isset($this->data['status']) && isset($this->data['createdAt']))
            return true;

        // customer
        if(isset($this->data['id']) && isset($this->data['ownId']) && isset($this->data['createdAt']))
            return true;

        return false;
    }

    public function getTransactionID()
    {
        if(isset($this->data['id'])) {
            return $this->data['id'];
        }

        return null;
    }

    public function getTransactionAuthorizationCode()
    {
        if(isset($this->data['acquirerDetails']['authorizationNumber'])) {
            return $this->data['acquirerDetails']['authorizationNumber'];
        }

        return null;
    }

    public function getResponseID()
    {
        if(isset($this->data['id'])) {
            return $this->data['id'];
        }

        return null;
    }

    /**
     * Get customer reference Id from createCustomer or createOrder requests
     *
     * @return string|null
     */
    /*public function getCustomerReference()
    {
        if(isset($this->data['customer']) && array_key_exists('id', $this->data['customer'])) {
            return $this->data['customer']['id'];
        } elseif (isset($this->data['id'])) {
            return (strtoupper( substr($this->data['id'], 0, 3) ) == 'CUS') ? $this->data['id'] : null;
        }

        return null;
    }*/

    /**
     * Get Order reference from createOrder
     *
     * @return string|null
     */
    public function getOrderId()
    {
        if(isset($this->data['order_id'])) {
            return $this->data['order_id'];
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getStatus()
    {
        return isset($this->data['status']) ? $this->data['status'] : null;
    }

    private function get($key, $default = null)
    {
        return isset($this->data[$key])
               ? $this->data[$key]
               : (isset($this->data['authorization'][$key])
                  ? $this->data['authorization'][$key]
                  : $default);

    }

    public function isPaid()
    {
        $status = @strtolower($this->getStatus());
        return (strcmp("authorized", $status)==0 || strcmp("settled", $status)==0);
    }

    public function isAuthorized()
    {
        $status = @strtolower($this->getStatus());
        return strcmp("pre_authorized", $status)==0;
    }

    public function isPending()
    {
        $status = @strtolower($this->getStatus());
        return (strcmp("waiting", $status)==0 || strcmp("in_analysis", $status)==0 || strcmp("created", $status)==0);
    }

    public function isVoided()
    {
        $status = @strtolower($this->getStatus());
        return strcmp("cancelled", $status)==0 || strcmp("refunded", $status)==0;
    }

    public function getCode()
    {
        return $this->get('returnCode');
    }

    public function getErrors()
    {
        return (isset($this->data['errors'])) ? $this->data['errors'] : null;
    }

    /**
     * Get the error message from the response.
     *
     * Returns null if the request was successful.
     *
     * @return string|null
     */
    public function getMessage()
    {
        return $this->getCode()." - ".$this->getErrors();

    }

    public function getBoleto()
    {
        $data = $this->getData();
        $boleto = array();
        $payment_id = @$data['payment_id'];
        $endpoint = $this->getTestMode()?$this->testEndpoint:$this->liveEndpoint;
        $boleto['boleto_url'] = "$endpoint/v1/payments/boleto/$payment_id/html";//@$data['boleto']['links'][0]['href']; //'https://api-sandbox.getnet.com.br/v1/payments/boleto/{payment_id}/html'
        $boleto['boleto_url_pdf'] = "$endpoint/v1/payments/boleto/$payment_id/pdf";//@$data['boleto']['links'][0]['href'];  //'https://api-sandbox.getnet.com.br/v1/payments/boleto/{payment_id}/pdf'
        $boleto['boleto_barcode'] = @$data['boleto']['typeful_line'];
        $boleto['boleto_expiration_date'] = @$data['boleto']['expiration_date'];
        $boleto['boleto_valor'] = (@$data['amount']*1.0)/100.0;
        $boleto['boleto_transaction_id'] = @$data['boleto']['boleto_id'];//@$data['payment_id']
        //@$this->setTransactionReference(@$data['transaction_id']);

        return $boleto;
    }

    public function getPix()
    {
        $data = $this->getData();
        $pix = array();
        $pix['pix_qrcodebase64image'] = $this->createPixImg(@$data['additional_data']['qr_code']);
        $pix['pix_qrcodestring'] = @$data['additional_data']['qr_code'];
        $pix['pix_valor'] = NULL;//(@$data['amount']*1.0)/100.0;
        $pix['pix_transaction_id'] = @$data['payment_id'];

        return $pix;
    }
}