<?php

namespace Omnipay\Moip\Message;


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
        if(isset($this->data['status']) && isset($this->data['created_at']))
            return true;

        return false;
    }

    public function getTransactionId()
    {
        if(isset($this->data['payment_id'])) {
            return $this->data['payment_id'];
        }

        return null;
    }

    public function getTransactionReference()
    {
        if(isset($this->data['payment_id'])) {
            return $this->data['payment_id'];
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

    /**
     * @return bool
     */
    public function isCreated()
    {
        return $this->getStatus() === 'CREATED';
    }

    /**
     * @return bool
     */
    public function isWaiting()
    {
        return $this->getStatus() === 'WAITING';
    }

    /**
     * @return bool
     */
    /*public function isPaid()
    {
        return $this->getStatus() === 'PAID';
    }*/

    /**
     * @return bool
     */
    /*public function isNotPaid()
    {
        return $this->getStatus() === 'NOT_PAID';
    }*/

    /**
     * @return bool
     */
    /*public function isReverted()
    {
        return $this->getStatus() === 'REVERTED';
    }*/

    /**
     * @return bool
     */
    public function isInAnalysis()
    {
        return $this->getStatus() === 'IN_ANALYSIS';
    }

    /**
     * @return bool
     */
    public function isPreAuthorized()
    {
        return $this->getStatus() === 'PRE_AUTHORIZED';
    }

    /**
     * @return bool
     */
    public function isAuthorized()
    {
        return $this->getStatus() === 'AUTHORIZED';
    }

    /**
     * @return bool
     */
    public function isCancelled()
    {
        return $this->getStatus() === 'CANCELLED';
    }

    /**
     * @return bool
     */
    /*public function isRefunded()
    {
        return $this->getStatus() === 'REFUNDED';
    }*/

    /**
     * @return bool
     */
    public function isReversed()
    {
        return $this->getStatus() === 'REVERSED';
    }

    /**
     * @return bool
     */
    public function isSettled()
    {
        return $this->getStatus() === 'SETTLED';
    }

    /**
     * @return array|null [['code' => 'ORD-001', 'path' => 'ownId', 'description' => 'Error message']]
     */
    public function getErrors()
    {
        return (isset($this->data['errors'])) ? $this->data['errors'] : null;
    }
}