<?php namespace Omnipay\Moip\Message;

use Exception;

class PurchaseRequest extends AuthorizeRequest
{
    public function getData()
    {
        $data = parent::getData();
        if(strcmp(strtolower($this->getPaymentType()), "creditcard")==0)
            $data["credit"]["delayed"] = false; // quando delayed=false já faz a autorização e captura ao mesmo tempo

        return $data;
    }

    /*public function getData()
    {
        $this->validate('order_id');
        $this->getCard()->validate();

        $data = [
//            'ownId'             => $this->getOrderOwnId(),
            'fundingInstrument' => $this->getFundingInstrumentData(),
//            'amount'            => ['currency' => $this->getCurrency()]
        ];

        $data['items'] = [];
        $items = $this->getItems();
        if ($items) {
            foreach ($items as $item) {
                $dataItem = [
                    'product'  => $item->getName(),
                    'quantity' => $item->getQuantity(),
                    'detail'   => $item->getDescription(),
                    'price'    => (int)($item->getPrice()*100.0)
                ];
                $data['items'][] = $dataItem;

            }
        }
        return $data;
    }*/
}