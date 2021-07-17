<?php
namespace Omnipay\Seamlesschex\Message;
use Omnipay\Seamlesschex\Exceptions\InvalidBankAccountException;

/**
 * Authorize Request
 *
 *
 * Example:
 *
 * <code>
 *   // Create a gateway for the Seamlesschex Gateway
 *   // (routes to GatewayFactory::create)
 *   $gateway = Omnipay::create('Seamlesschex');
 *
 *   // Initialise the gateway
 *   $gateway->initialize(array(
 *       'apiKey' => 'MyApiKey',
 *   ));
 *
 *   // Create a credit card object
 *   // This card can be used for testing.
 *   $bank = new BankAccount(array(
 *               'firstName'    => 'Example',
 *               'lastName'     => 'Customer',
 *               'number'       => '1111000016', //Bank account number
 *               'routing'      => '999900001',
 *               'email'                 => 'customer@example.com',
 *               'phone'                 => '000-000-0000',
 *               'billingAddress1'       => '1 Scrubby Creek Road',
 *               'billingCountry'        => 'AU',
 *               'billingCity'           => 'Scrubby Creek',
 *               'billingPostcode'       => '4999',
 *               'billingState'          => 'QLD',
 *   ));
 *
 * OR Token Usage:
 *
 *  $bank = new BankAccount(array(
 *               'firstName'    => 'Example',
 *               'lastName'     => 'Customer',
 *               'token'        => 'asfaifn9ausengv9awsn', //Token from Tokenization Request
 *               'email'                 => 'customer@example.com',
 *               'phone'                 => '000-000-0000',
 *               'billingAddress1'       => '1 Scrubby Creek Road',
 *               'billingCountry'        => 'AU',
 *               'billingCity'           => 'Scrubby Creek',
 *               'billingPostcode'       => '4999',
 *               'billingState'          => 'QLD',
 *   ));
 *
 *   // Do an authorize transaction on the gateway
 *   $transaction = $gateway->purchase(array(
 *       'amount'                   => '10.00',
 *       'memo'                     => 'This is a test transaction.',
 *       'card'                     => $bank,
 *       'store'                    => 'store.com', //Store var passed to Tokenization request
 *       'verify_before_save'       => 1,
 *       'fund_confirmation'        => 0
 *   ));
 *   $response = $transaction->send();
 *   if ($response->isSuccessful()) {
 *       echo "Authorize transaction was successful!\n";
 *       $sale_id = $response->getTransactionReference();
 *       echo "Transaction reference = " . $sale_id . "\n";
 *   }
 * </code>
 *
 *
 * @method Response send()
 */
class PurchaseRequest extends AbstractRequest
{
    /**
     * @return array|mixed
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @throws InvalidBankAccountException
     */
    public function getData()
    {
        $this->validate('amount', 'memo');
        $data = $this->getBaseData();
        $value = $this->parameters->get('token');
        if (isset($value)) {
            $this->validate('token', 'store');
            foreach(['token', 'store'] as $key) {
                $value = $this->getParameter($key);
                if (isset($value)) {
                    $data[$key] = $value;
                }
            }
        } else {
            //$this->validate('bank_account', 'bank_routing');
            $this->getBank()->validate();
            $data['bank_account'] = $this->getCard()->getBankAccount();
            $data['bank_routing'] = $this->getCard()->getBankRouting();
        }

        foreach(['number', 'authorization_date', 'label', 'recurring', 'recurring_cycle', 'recurring_state_date', 'recurring_installments', 'verify_before_save', 'fund_confirmation'] as $key) {
            $value = $this->getParameter($key);
            if (isset($value)) {
                $data[$key] = $value;
            }
        }

        foreach(['phone', 'city', 'state', 'zip'] as $key) {
            $_method = 'get' . ucfirst(strtolower($key));
            $value = $this->getCard()->{$_method}();
            if (isset($value)) {
                $data[$key] = $value;
            }
        }
        if ($this->getCard()->getAddress1()) {
            $data['address'] = $this->getCard()->getAddress1() . ($this->getCard()->getAddress2() ? ' ' . $this->getCard()->getAddress2() : '');
        }
        return $data;
    }

    /**
     * @return array
     */
    protected function getBaseData()
    {
        return [
            'amount' => $this->getAmount(),
            'memo' => $this->getParameter('memo'),
            'email' => $this->getCard()->getEmail(),
            'name' => $this->getCard()->getName()
        ];
    }

    /**
     * @param $data
     * @param array $headers
     * @return CreateCheckResponse
     */
    protected function createResponse($data, $headers = [])
    {
        return $this->response = new CreateCheckResponse($this, $data, $headers);
    }

    /**
     * @return string
     */
    protected function getEndpoint()
    {
        return parent::getEndpoint() . 'check/create';
    }
}
