<?php

namespace Omnipay\Seamlesschex\Exceptions;

/**
 * Invalid Bank Account Exception
 *
 * Thrown when a Bank Account is invalid or missing required fields.
 */
class InvalidBankAccountException extends \Exception implements \Omnipay\Common\Exception\OmnipayException
{
}
