<?php
namespace Omnipay\Seamlesschex\Message;

use Omnipay\Seamlesschex\Exceptions\InvalidBankAccountException;

/**
 * Tokenization Request
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
 *               'billingAddress1'       => '1 Scrubby Creek Road',
 *               'billingCountry'        => 'AU',
 *               'billingCity'           => 'Scrubby Creek',
 *               'billingPostcode'       => '4999',
 *               'billingState'          => 'QLD',
 *   ));
 *
 *   // Do an authorize transaction on the gateway
 *   $transaction = $gateway->authorize(array(
 *       'card'                     => $bank,
 *       'store'                    => 'store.com' //pass this same value to the Authorize Request
 *   ));
 *   $response = $transaction->send();
 *   if ($response->isSuccessful()) {
 *       echo "Token transaction was successful!\n";
 *       $token = $response->getToken());
 *       echo "Token reference = " . $token . "\n";
 *   }
 * </code>
 *
 * @method TokenizationResponse send()
 */
class TokenizationRequest extends AbstractRequest
{
    /**
     * @return array
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @throws InvalidBankAccountException
     */
    public function getData()
    {
        $this->validate('store');
        $this->getCard()->validateTokenRequest();
        $data = $this->getBaseData();
        foreach(['email', 'phone', 'store'] as $key) {
            $value = $this->getParameter($key);
            if (isset($value)) {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * @param $data
     * @param array $headers
     * @return TokenizationResponse
     */
    protected function createResponse($data, $headers = [])
    {
        return $this->response = new TokenizationResponse($this, $data, $headers);
    }

    /**
     * @return array
     */
    protected function getBaseData()
    {
        return [
            'first_name' => $this->getCard()->getFirstName(),
            'last_name' => $this->getCard()->getLastName(),
            'bank_account' => $this->getCard()->getBankAccount(),
            'bank_routing' => $this->getCard()->getBankRouting(),
        ];
    }

    /**
     * @return string
     */
    protected function getEndpoint()
    {
        return parent::getEndpoint() . 'account/tokenization';
    }
}
