<?php

namespace Omnipay\Seamlesschex\Message;

use Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;
use Omnipay\Seamlesschex\BankAccount;

/**
 * Abstract Request
 *
 */
abstract class AbstractRequest extends BaseAbstractRequest
{
    protected $liveEndpoint = 'https://api.seamlesschex.com/v1/';
    protected $testEndpoint = 'https://sandbox.seamlesschex.com/v1/';

    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }

    public function setApiKey($value)
    {
        return $this->setParameter('apiKey', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function sendData($data)
    {
        $headers = $this->getHeaders();

        $body = $data ? json_encode($data) : null;
        $httpResponse = $this->httpClient->request(
            $this->getHttpMethod(),
            $this->getEndpoint(),
            $headers,
            $body
        );

        return $this->createResponse($httpResponse->getBody()->getContents(), $httpResponse->getHeaders());
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        $headers = array(
            'Authorization' => 'Bearer ' . $this->getApiKey(),
            'Content-Type' => 'application/json'
        );

        return $headers;
    }

    /**
     * Get HTTP Method.
     *
     * This is nearly always POST but can be over-ridden in sub classes.
     *
     * @return string
     */
    public function getHttpMethod()
    {
        return 'POST';
    }

    protected function getBaseData()
    {
        return [
            /*'transaction_id' => $this->getTransactionId(),
            'expire_date' => $this->getCard()->getExpiryDate('mY'),
            'start_date' => $this->getCard()->getStartDate('mY'),*/
        ];
    }

    protected function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    protected function createResponse($data, $headers = [])
    {
        return $this->response = new Response($this, $data, $headers);
    }

    /**
     * Get the bank.
     *
     * @return BankAccount
     */
    public function getCard()
    {
        return $this->getParameter('card');
    }

    /**
     * Get the bank.
     *
     * @return BankAccount
     */
    public function getBank()
    {
        return $this->getParameter('card');
    }
}
