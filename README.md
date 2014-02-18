# PHP Payments : extended support for Braintree

The original *PHP-Payments* source, documentation and license can be found [here](https://github.com/calvinfroedge/PHP-Payments)


## Recurring Billing
[Braintree documentation](https://www.braintreepayments.com/docs/php/subscriptions/create)

Subscribing an existing customer in the vault to an existing plan:

```php
$response = $payments->recurring_payment('braintree', $params);
```

### parameters accepted ###

Minimal example:

```php
'profile_reference' =>	'planId',  //Plan id the customer is being subscribed to
'identifier'        =>	'paymentMethodToken' //Token for the credit card stored in the Vault
```

Custom Subscription ID:

```php
'profile_reference' =>	'planId',  //Plan id the customer is being subscribed to
'identifier'        =>	'paymentMethodToken', //Token for the credit card stored in the Vault
'inv_num'           =>	'id', //custom subscription id
```

Overriding Plan Price:

```php
'profile_reference' =>	'planId',  //Plan id the customer is being subscribed to
'identifier'        =>	'paymentMethodToken', //Token for the credit card stored in the Vault
'amt'               =>  'price' //override the plan price
```

Overriding Plan Trial duration:

```php
'profile_reference'         =>	'planId',  //Plan id the customer is being subscribed to
'identifier'                =>	'paymentMethodToken', //Token for the credit card stored in the Vault
'trial_billing_cycles'      =>  'trialDuration', //if 0 the subscription will start immediately and the customer charged
'trial_billing_frequency'   =>  'trialDurationUnit' //valid values are day and month
```
Note: if the trial period is disabled for the given plan, the trial duration defined will be ignored.



## LICENSE

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
