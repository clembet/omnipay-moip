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
        return strcmp("cancelled", $status)==0 || strcmp("refunded", $status)==0 || strcmp("completed", $status)==0;
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
        $erros = $this->getErrors();
        $erroMsg = "";
        if(is_array($erros)){
            $erroMsg = 'Erro '.$erros[0]['code'].' - '.$erros[0]['description'];
        }
        return $this->getCode()." - ".$erroMsg;

    }

    public function getBoleto()
    {
        $data = $this->getData();
        $boleto = array();
        $boleto['boleto_url'] = @$data['_links']['payBoleto']['printHref'];
        $boleto['boleto_url_pdf'] = @$data['_links']['payBoleto']['printHref'];
        $boleto['boleto_barcode'] = @$data['fundingInstrument']['boleto']['lineCode'];
        $boleto['boleto_expiration_date'] = @$data['fundingInstrument']['boleto']['expirationDate'];
        $boleto['boleto_valor'] = (@$data['amount']['total']*1.0)/100.0;
        $boleto['boleto_transaction_id'] = @$data['id'];
        //@$this->setTransactionReference(@$data['transaction_id']);

        return $boleto;
    }

    public function getPix()
    {
        return [];
    }
}