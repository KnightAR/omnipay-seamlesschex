<?php
namespace Omnipay\Seamlesschex;

use DateTime;
use DateTimeZone;
use Omnipay\Common\CreditCard;
use Omnipay\Common\Exception\InvalidCreditCardException;
use Omnipay\Seamlesschex\Exceptions\InvalidBankAccountException;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Credit Card class
 *
 * This class defines and abstracts all of the credit card types used
 * throughout the Omnipay system.
 *
 * Example:
 *
 * <code>
 *   // Define credit card parameters, which should look like this
 *   $parameters = [
 *      bank_account
 *      bank_routing
 *   ];
 *
 *   // Create a credit card object
 *   $card = new CreditCard($parameters);
 * </code>
 *
 * The full list of card attributes that may be set via the parameter to
 * *new* is as follows:
 *
 * * bank_account
 * * bank_routing
 * If any unknown parameters are passed in, they will be ignored.  No error is thrown.
 */
class BankAccount extends CreditCard
{
    /**
     * Validate this bank account. If the bank info is invalid, InvalidBankAccountException is thrown.
     *
     * This method is called internally by gateways to avoid wasting time with an API call
     * when the credit card is clearly invalid.
     *
     * Generally if you want to validate the credit card yourself with custom error
     * messages, you should use your framework's validation library, not this method.
     *
     * @return void
     * @throws InvalidBankAccountException
     */
    public function validate()
    {
        $requiredParameters = array(
            'number' => 'bank account number',
            'bank_routing' => 'bank account routing number',
            'billingFirstName' => 'first name',
            'billingLastName' => 'last name',
            'email' => 'email address'
        );

        foreach ($requiredParameters as $key => $val) {
            if (!$this->getParameter($key)) {
                throw new InvalidBankAccountException("The $val is required");
            }
        }

        if (!is_null($this->getBankAccount()) && !preg_match('/^\d{4,17}$/i', $this->getBankAccount())) {
            throw new InvalidBankAccountException('Bank Account number should have 4 to 17 digits');
        }

        if (!is_null($this->getBankRouting()) && !preg_match('/^\d{9}$/i', $this->getBankRouting())) {
            throw new InvalidBankAccountException('Bank Account number should be 9 digits');
        }
    }

    /**
     * @return string
     */
    public function getZip() {
        return $this->getPostcode();
    }

    /**
     * @throws InvalidBankAccountException
     */
    public function validateTokenRequest()
    {
        $requiredParameters = array(
            'billingFirstName' => 'first name',
            'billingLastName' => 'last name',
            'number' => 'bank account number',
            'bank_routing' => 'bank account routing number',
        );

        foreach ($requiredParameters as $key => $val) {
            if (!$this->getParameter($key)) {
                throw new InvalidBankAccountException("The $val is required");
            }
        }

        if (!is_null($this->getBankAccount()) && !preg_match('/^\d{4,17}$/i', $this->getBankAccount())) {
            throw new InvalidBankAccountException('Bank Account number should have 4 to 17 digits');
        }

        if (!is_null($this->getBankRouting()) && !preg_match('/^\d{9}$/i', $this->getBankRouting())) {
            throw new InvalidBankAccountException('Bank Account number should be 9 digits');
        }
    }

    /**
     * Get bank account number.
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->getParameter('number');
    }

    /**
     * Set Bank Account Number
     *
     * Non-numeric characters are stripped out of the card number, so
     * it's safe to pass in strings such as "1111000016" etc.
     *
     * @param string $value Parameter value
     * @return $this
     */
    public function setNumber($value)
    {
        // strip non-numeric characters
        return $this->setParameter('number', preg_replace('/\D/', '', $value));
    }

    /**
     * Get Card Number.
     *
     * @return string
     */
    public function getBankAccount()
    {
        return $this->getNumber();
    }

    public function setBankAccount($value)
    {
        return $this->setNumber($value);
    }

    /**
     * Get the last 4 digits of the card number.
     *
     * @return string
     */
    public function getNumberLastFour()
    {
        return substr($this->getBankAccount(), -4, 4) ?: null;
    }

    /**
     * Returns a masked credit card number with only the last 4 chars visible
     *
     * @param string $mask Character to use in place of numbers
     * @return string
     */
    public function getNumberMasked($mask = 'X')
    {
        $maskLength = strlen($this->getBankAccount()) - 4;

        return str_repeat($mask, $maskLength) . $this->getNumberLastFour();
    }

    /**
     * Get the Routing number.
     *
     * @return string
     */
    public function getBankRouting()
    {
        return $this->getParameter('bank_routing');
    }

    /**
     * Sets the Routing number.
     *
     * @param string $value
     * @return $this
     */
    public function setBankRouting($value)
    {
        return $this->setParameter('bank_routing', $value);
    }


    /**
     * Get the Routing number.
     *
     * @return string
     */
    public function getRouting()
    {
        return $this->getParameter('bank_routing');
    }

    /**
     * Sets the Routing number.
     *
     * @param string $value
     * @return $this
     */
    public function setRouting($value)
    {
        return $this->setParameter('bank_routing', $value);
    }
}
