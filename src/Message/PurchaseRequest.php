<?php namespace Omnipay\Moip\Message;

use Exception;

class PurchaseRequest extends AuthorizeRequest
{
    public function getData()
    {
        $data = parent::getData();
        if(strcmp(strtolower($this->getPaymentType()), "creditcard")==0) {
            $data["delayCapture"] = false; // quando delayCapture=false já faz a autorização e captura ao mesmo tempo
        }

        return $data;
    }
}