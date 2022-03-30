<?php namespace Omnipay\Moip\Message;


class CreateCustomerRequest extends AbstractRequest
{
    protected $resource = 'customers';

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     * @throws \Omnipay\Common\Exception\InvalidCreditCardException
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData()
    {
        $this->validate('customer');
        $customer = $this->getCustomer();

        $data = [
            'ownId'             => $this->getCustomerOwnId(),
            'fullname'          => $customer->getName(),
            'email'             => $customer->getEmail(),
            'birthDate'         => $customer->getBirthday(),//formato yyyy-mm-dd
            'taxDocument'       => [
                'type'          => 'CPF',
                'number'        => $customer->getDocumentNumber(),
            ],
            'phone'             => $this->getPhoneParams($customer),
            'shippingAddress'   => $this->getShippingParams($customer),
            //'fundingInstrument' => $this->getFundingInstrumentData(),
        ];

        return $data;
    }

    public function getBillingParams($customer)
    {
        return [
            'city'         => $customer->getBillingCity(),
            'district'     => $customer->getBillingDistrict(),
            'street'       => $customer->getBillingAddress1(),
            'streetNumber' => $customer->getBillingNumber(),
            'zipCode'      => $customer->getBillingPostcode(),
            'state'        => $customer->getBillingState(),
            'country'      => $customer->getBillingCountry(),
        ];
    }

    public function getShippingParams($customer)
    {
        return [
            'city'         => ($customer->getShippingCity()) ? $customer->getShippingCity() : $customer->getBillingCity(),
            'district'     => ($customer->getShippingDistrict()) ? $customer->getShippingDistrict() : $customer->getBillingDistrict(),
            'street'       => ($customer->getShippingAddress1()) ? $customer->getShippingAddress1() : $customer->getBillingAddress1(),
            'streetNumber' => ($customer->getShippingNumber()) ? $customer->getShippingNumber() : $customer->getBillingNumber(),
            'complement'   => ($customer->getShippingAddress2()) ? $customer->getShippingAddress2() : $customer->getShippingAddress2(),
            'zipCode'      => ($customer->getShippingPostcode()) ? $customer->getShippingPostcode() : $customer->getBillingPostcode(),
            'state'        => ($customer->getShippingState()) ? $customer->getShippingState() : $customer->getBillingState(),
            'country'      => ($customer->getShippingCountry()) ? $customer->getShippingCountry() : $customer->getBillingCountry(),
        ];
    }

    /**
     * @param \Omnipay\Common\CreditCard $card
     *
     * @return array
     */
    public function getPhoneParams($customer)
    {
        return [
            'countryCode' => '55',
            'areaCode'    => $customer->getAreaCode(),
            'number'      => substr($customer->getPhone(), 2),
        ];
    }

    public function getFundingInstrumentData()
    {
        $this->validate('paymentType');

        $data = [
            'method' => $this->getPaymentType()=='Boleto'?'BOLETO':'CREDIT_CARD',
        ];

        if ($this->getPaymentType() == 'Boleto') {
            $data['boleto'] = $this->getBoletoData();
        } else {
            $data['creditCard'] = $this->getCardData();
        }

        return $data;
    }

    protected function getCardData()
    {
        $card = $this->getCard();
        $customer = $this->getCustomer();
        
        $data = [
            'number' => $card->getNumber(),
            'expirationMonth' => $card->getExpiryMonth(),
            'expirationYear' => $card->getExpiryYear(),
            'cvc' => $card->getCvv(),
        ];

        $data['holder'] = [
            'fullname'       => $card->getName(),
            'birthdate'      => $card->getBirthday(),
            'taxDocument'       => [
                'type'          => 'CPF',
                'number'        => $card->getHolderDocumentNumber(),
                ],
            'phone'          => $this->getPhoneParams($card),
            'billingAddress' => $this->getBillingParams($card),
            
        ];

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
}