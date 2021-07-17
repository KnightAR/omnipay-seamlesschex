<?php

namespace Omnipay\Seamlesschex\Message;

/**
 * CreateCheckResponse
 */
class CreateCheckResponse extends Response
{
    public function getCheck()
    {
        if (isset($this->data['check'])) {
            return $this->data['check'];
        }
    }

    public function getTransactionReference()
    {
        $check = $this->getCheck();
        if ($check && isset($check['check_id'])) {
            return $check['check_id'];
        }
    }

}
