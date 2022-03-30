<?php namespace Omnipay\Moip\Message;


use Omnipay\Common\Message\ResponseInterface;

class AuthorizeRequest extends AbstractRequest
{
    protected $resource = 'orders';

    public function getData()
    {
        $this->validate('customer', 'paymentType', 'order_id');

        $data = [];
        switch(strtolower($this->getPaymentType()))
        {
            case 'creditcard':
                $data = $this->getDataCreditCard();
                break;

            case 'boleto':
                $data = $this->getDataBoleto();
                break;

            case 'pix':
                //$data = $this->getDataPix();
                break;

            default:
                $data = $this->getDataCreditCard();
        }

        return $data;
    }

    protected function getEndpoint()//os pagamentos se referem a um pedido (order)
    {
        $endPoint = parent::getEndpoint();
        return  "{$endPoint}/{$this->getOrderID()}/payments";
    }

}