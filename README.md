# PHP Payments : extended support for Braintree

The original *PHP-Payments* source, documentation and license can be found [here](https://github.com/calvinfroedge/PHP-Payments)


## Create Customer in vault
[Braintree documentation](https://www.braintreepayments.com/docs/php/customers/create)


```php
$response = $payments->customer_create('braintree', $params);
```

Minimal example:

```php
$params = array(
    'first_name' =>	'firstName'     //Customer's first name
);
```

Customer with Credit Card:

```php
$params = array(
    'first_name' =>	'firstName',    //Customer's first name
    'last_name' =>	'lastName',     //Customer's last name
    'email'     =>	'email',        //Customer's email
    'cc_number' =>  'creditCard["number"]', //Credit card number
    'cc_exp'    =>  'creditCard["expirationDate"]', //Format MMYYYY
    'cc_code'   =>  'creditCard["cvv"]'  //3 or 4 digit cvv code
);
```

Access newly created customer's data:

```php
//customer Id
$response->details->identifier;

//payment method token
$gatewayResponse = $response->details->gateway_response;
$creditCard = $gatewayResponse->customer->__get('creditCards')[0];
$token = $creditCard->__get('token');
```


## Create Credit Card in vault
[Braintree documentation](https://www.braintreepayments.com/docs/ruby/credit_cards/create)


```php
$response = $payments->token_create('braintree', $params);
```

```php
$params = array(
    'custom'    =>	'customerId',    //Customer id to whom the card is being associated
    'cc_number' =>  'creditCard["number"]', //Credit card number
    'cc_exp'    =>  'creditCard["expirationDate"]', //Format MMYYYY
    'cc_code'   =>  'creditCard["cvv"]'  //3 or 4 digit cvv code (optional)
);
```

Access created card's token:

```php
$response->details->identifier;
```


## Recurring Billing
[Braintree documentation](https://www.braintreepayments.com/docs/php/subscriptions/create)

Subscribe an existing customer in the vault to an existing plan:

```php
$response = $payments->recurring_payment('braintree', $params);
```


Minimal example:

```php
$params = array(
    'profile_reference' =>	'planId',  //Plan id the customer is being subscribed to
    'identifier'        =>	'paymentMethodToken' //Token for the credit card stored in the Vault
);
```

Custom Subscription ID:

```php
$params = array(
    'profile_reference' =>	'planId',  //Plan id the customer is being subscribed to
    'identifier'        =>	'paymentMethodToken', //Token for the credit card stored in the Vault
    'inv_num'           =>	'id' //custom subscription id
);
```

Overriding Plan Price:

```php
$params = array(
    'profile_reference' =>	'planId',  //Plan id the customer is being subscribed to
    'identifier'        =>	'paymentMethodToken', //Token for the credit card stored in the Vault
    'amt'               =>  'price' //override the plan price
);
```

Overriding Plan Trial duration:

```php
$params = array(
    'profile_reference'         =>	'planId',  //Plan id the customer is being subscribed to
    'identifier'                =>	'paymentMethodToken', //Token for the credit card stored in the Vault
    'trial_billing_cycles'      =>  'trialDuration', //if 0 the subscription will start immediately and the customer charged
    'trial_billing_frequency'   =>  'trialDurationUnit' //valid values are day and month
);
```
Note: if the trial period is disabled for the given plan, the trial parameters will be ignored.


Cancel a plan subscription:

```php
$params = array(
    'identifier'    =>	'id'  //The id of the subscription to be canceled
);

$response = $payments->cancel_recurring_profile('braintree', $params);
```



### Testing ###

* no testing was implemented for these methods*

## LICENSE

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
