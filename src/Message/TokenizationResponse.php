<?php

namespace Omnipay\Seamlesschex\Message;

/**
 * TokenizationResponse
 */
class TokenizationResponse extends Response
{
    public function getTransactionReference()
    {
        return $this->getToken();
    }

    public function getTokenization()
    {
        if (isset($this->data['tokenization'])) {
            return $this->data['tokenization'];
        }
    }

    public function getToken()
    {
        $tokenization = $this->getTokenization();
        if ($tokenization && isset($tokenization['token'])) {
            return $tokenization['token'];
        }
    }

    public function getBank()
    {
        $tokenization = $this->getTokenization();
        if ($tokenization && isset($tokenization['bank'])) {
            return $tokenization['bank'];
        }
    }

    public function getRouting()
    {
        $tokenization = $this->getTokenization();
        if ($tokenization && isset($tokenization['routing'])) {
            return $tokenization['routing'];
        }
    }

    public function getLast4()
    {
        $tokenization = $this->getTokenization();
        if ($tokenization && isset($tokenization['last4'])) {
            return $tokenization['last4'];
        }
    }


    public function getName()
    {
        $tokenization = $this->getTokenization();
        if ($tokenization && isset($tokenization['name'])) {
            return $tokenization['name'];
        }
    }
}
