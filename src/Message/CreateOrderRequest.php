<?php namespace Omnipay\Moip\Message;


class CreateOrderRequest extends CreateCustomerRequest
{
    protected $resource = 'orders';


    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @throws \Omnipay\Common\Exception\InvalidCreditCardException
     */
    public function getData()
    {
        $this->validate('amount', 'currency', 'items', 'orderOwnId', 'customerReference', 'shipping_price');

        $data = [
            'ownId'    => $this->getOrderOwnId(),
            'amount'   => [
                'currency' => $this->getCurrency(),
                'subtotals' => [
                    'shipping' => (int)($this->getShippingPrice()*100.0),
                    'addition' => 0,
                    'discount' => 0,
                ]
            ],
            'items'    => [],
            'customer' => []
        ];

        if ($this->getCustomerReference()) {
            $data['customer']['id'] = $this->getCustomerReference();
        } else {
            $data['customer'] = parent::getData();
        }

        if(array_key_exists('fundingInstrument', $data['customer'])) {
            unset($data['customer']['fundingInstrument']);
        }

        $items = $this->getItems();
        if ($items) {
            foreach ($items as $item) {
                $dataItem = [
                    'product'  => $item->getName(),
                    'quantity' => $item->getQuantity(),
                    'detail'   => "",//$item->getDescription(),
                    'price'    => (int)($item->getPrice()*100.0)
                ];
                $data['items'][] = $dataItem;

            }
        }

        return $data;
    }
}