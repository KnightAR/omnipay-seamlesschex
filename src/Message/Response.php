<?php

namespace Omnipay\Seamlesschex\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * Response
 */
class Response extends AbstractResponse
{
    public function __construct(RequestInterface $request, $data, $headers)
    {
        $this->request = $request;
        $this->data = \GuzzleHttp\json_decode($data, true);
        $this->headers = $headers;
    }

    public function isSuccessful()
    {
        return isset($this->data['success']);
    }
}
