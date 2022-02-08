<?php
/**
 * omipay-moip
 * Created by alvaro.
 * User: alvaro
 * Date: 26/03/19
 * Time: 06:32 AM
 */

namespace Omnipay\Moip\Message;

use Exception;
use Moip\Moip;
use Moip\Auth\BasicAuth;


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

class PurchaseRequest extends CreateOrderRequest
{
    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return array
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @throws \Omnipay\Common\Exception\InvalidCreditCardException
     */
    public function getData()
    {
        /** @var \Omnipay\Common\Item $item */

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
    }

    public function sendData($data)
    {
        $moip = new Moip(new BasicAuth($this->getToken(), $this->getApiKey()), $this->getEndpoint());

        $card_data = $this->getCardData();
        $customer_data = $card_data['customer'];
        $holder_data = $card_data['holder'];
        $billingAddress = $holder_data['billingAddress'];
        $items = $this->getItems();
        $result = [];

        //print_r($card_data);
        //print_r($items);
        //exit();

        try {
            /*
             * If you want to persist your customer data and save later, now is the time to create it.
             * TIP: Don't forget to generate your `ownId` or use one you already have,
             * here we set using uniqid() function.
             */
            $customer = $moip->customers()->setOwnId(uniqid())//uniqid()
            ->setFullname($customer_data['name'])
                ->setEmail($customer_data['email'])
                ->setBirthDate($customer_data['birthday'])
                ->setTaxDocument($customer_data['doc'])
                ->setPhone(@substr($customer_data['phone'], 0, 2), @substr($customer_data['phone'], 2, 9))
                ->addAddress('SHIPPING', $customer_data['address_line1'], $customer_data['address_number'], $customer_data['address_district'], $customer_data['address_city'], $customer_data['address_state'], $customer_data['address_zip'], $customer_data['address_line2'])
                ->create();

            $holder = $moip->holders()
                ->setFullname($holder_data['fullname'])
                ->setBirthDate($holder_data['birthdate'])
                ->setTaxDocument($holder_data['taxDocument']['number'])
                ->setPhone(@substr($holder_data['phone']['number'], 0, 2), @substr($holder_data['phone']['number'], 2, 9))
                ->setAddress('BILLING', $billingAddress['street'], $billingAddress['streetNumber'], $billingAddress['district'], $billingAddress['city'], $billingAddress['state'], $billingAddress['zipCode'], '');

            // Creating an order
            $order = $moip->orders()->setOwnId(uniqid());//uniqid()

            if ($items) {
                foreach ($items as $item) {
                    //$order->addItem('bicicleta 1', 1, 'sku1', 10000)
                    $order->addItem($item->getName(), (int)($item->getQuantity()*1), $item->getDescription(), (int)($item->getPrice()*100.0));
                }
            }

            $order->setShippingAmount((int)($this->getShippingAmount()*100));
            //->setAddition(1000)
            //->setDiscount(5000)
            $order->setCustomer($customer)->create();

            /*
            * Don't forget you must generate your hash to encrypt credit card data using https://github.com/moip/moip-sdk-js
             *
             * <script src="moip-sdk-js.js"></script>
                import jsencrypt from 'jsencrypt';
                import { MoipCreditCard } from 'moip-sdk-js';

                MoipCreditCard
                    .setEncrypter(jsencrypt, 'ionic') // quando usa o jsencrypt
                    .setPubKey($this->pubKey)
                    .setCreditCard({
                        number: '4012001037141112',
                        cvc: '123',
                        expirationMonth: '05',
                        expirationYear: '22'
                    })
                    .hash()
                    .then(hash => console.log('hash', hash));

            //a função encripta uma string no formato: "number=4012001037141112&cvc=123&expirationMonth=05&expirationYear=22"
		    //com RSA de 2048bits

            Bandeira dos cartões
            MoipValidator.cardType('5105105105105100');    //return [Object]MASTERCARD
            MoipValidator.cardType('4111111111111111');    //return [Object]VISA
            MoipValidator.cardType('341111111111111');     //return [Object]AMEX
            MoipValidator.cardType('30569309025904');      //return [Object]DINERS
            MoipValidator.cardType('3841001111222233334'); //return [Object]HIPERCARD
            MoipValidator.cardType('4514160123456789');    //return [Object]ELO
            MoipValidator.cardType('6370950000000005');    //return [Object]HIPER
            MoipValidator.cardType('9191919191919191');    //return [Object]null
            */


            //$hash = 'Nahw17ywGZvq2gP3t8RZJjwppP0rikDTmlTe7ccJIn9HOOU8JO/ePUfS/rE7pzNyJoP3MucYqPD3dvCRoCuDd0e37uusNsupk21Q2H95RVq8VGyBildZv1gD2vs4PoXYSitGfVCfOfcwRKmFVrmkZYwp+UdBnH95QH9WZLjvDUI7lBKdhfrRrMQdEj1MEGh+1xXAcX35P+yYX1CsW4aj9fw3MZkxx2pM9Hr5w17Ye3ki5pNb4DUGnU3BkO1Mr8xv2LxcgHXWETbZMBPew7xZEt6rQFOHrwyHD+fDKeADDphOySmLso5hQBZuB05aWqWsaN0zTyhGpIolWLO4p81+qA==';
            //$hash = 'ZHt7FXfwhlnKhorUuwuBXf8iF8NgQF8a9umhLFFVKQefQxwk/tus6biORkdBY1fy/s2CROJMFpp0/JSCkWpctCKck13Zbi9jnkmn3msi6cb4lAGxFJZs432k8gbE9cKLBfkOuixaa3PTo2Yzl5q7fWb19H7WJLLrCPtByC05lyCcUxSslifkVfF0+ADHHamSDx2wPbkcX8tZT/AhTyCwmm1tlPTscilgLM2wCiEjUt3yosDpdKnNsws9FxgnJkVc3tCsKfh3Tv1IKGDaou5aLqiylNIE1eLfpsIytlywGfF6/6y8ZncvSquPUvLsLcr8HGR8lFZNJu0PolZYxBEgsQ==';

            $data = "number=".$card_data['number']."&cvc=".$card_data['cvc']."&expirationMonth=".sprintf("%02d", $card_data['expirationMonth']*1)."&expirationYear=".substr($card_data['expirationYear'], 2, 2);
            $hash = rsa_encrypt($data, $this->pubKey);
            //print "$hash\n";exit();


            // Creating payment to order
            $payment = $order->payments()
                ->setCreditCardHash($hash, $holder)
                ->setInstallmentCount($card_data['installments']*1)
                ->setStatementDescriptor('BelezaTodoDia')//TODO: parametrizar
                ->execute();

            //para esse método precisa de certificação PCI
            /*$payment = $order->payments()
                ->setCreditCard(12, 21, '4073020000000002', '123', $holder)
                ->setInstallmentCount(3)
                ->setStatementDescriptor('teste de pag')
                ->execute();*/

            $result = [];
            $result['order_id'] = $order->getId();
            $result['payment_id'] = $payment->getId();
            $result['created_at'] = $payment->getCreatedAt()->format('Y-m-d H:i:s');
            $result['status'] = $payment->getStatus(); //* @return string Payment status. Possible values CREATED, WAITING, IN_ANALYSIS, PRE_AUTHORIZED, AUTHORIZED, CANCELLED, REFUNDED, REVERSED, SETTLED
            $result['amount'] = $payment->getAmount()->total;
            $result['funding_instrument'] = $payment->getFundingInstrument()->method;
            $result['installment_count'] = $payment->getInstallmentCount();

        } catch (Exception $e) {
            return $this->response = $this->createResponse(['errors'=>$e->getMessage()]);
        }

        return $this->response = $this->createResponse(@$result);
    }

    protected function getEndpoint()
    {
        return parent::getEndpoint();
    }
}