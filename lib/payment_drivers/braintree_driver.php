<?php

class Braintree_Driver extends Payment_Driver
{
    /**
     * URL to Use
     */
    private $_api_url;

    /**
     * Endpoint to Send the Query To
     */
    private $_endpoint;

    /**
     * The particular API which is being used
     */
    private $_api;

    /**
     * The particular API method which is being used
     */
    private $_api_method;

    /**
     * The PHP Payments library method
     */
    private $_lib_method;

    /**
     * Stores Settings for the Transaction
     */
    private $_settings;

    /**
     * The Constructor Function
     */
    public function __construct($config)
    {
        Payment_Utility::load('file', 'vendor/braintree/lib/Braintree');

        $bt_mode = ($config['mode'] == 'test') ? 'sandbox' : 'production';
        Braintree_Configuration::environment($bt_mode);
        unset($config['mode']);

        foreach($config as $k=>$v)
        {
            //merchantId, publicKey, privateKey need to be set
            Braintree_Configuration::$k($v);
        }
    }

    /**
     * Call Magic Method
     */
    public function __call($method, $params)
    {
        $args = $params[0];

        $method_map = $this->method_map();

        $this->_api = $method_map[$method]['api'];
        $this->_api_method = (isset($method_map[$method]['method'])) ? $method_map[$method]['method'] : '';
        $this->_lib_method = $method;

        list($api, $api_method, $params_ready) = $this->_build_request($args);

        try {
            //If the only param is an id, just use that as a string instead of sending an array
            if(count($params_ready) == 1 && isset($params_ready['id']))
            {
                $response_raw = $api::$api_method($params_ready['id']);
            }
            else if(count($params_ready) == 2 && isset($params_ready['id']) && isset($params_ready['amount']))
            {
                $response_raw = $api::$api_method($params_ready['id'], $params_ready['amount']);
            }
            else
            {
                $response_raw = $api::$api_method($params_ready);
            }
        }
        catch (Exception $e) {
            if(get_class($e) == 'Braintree_Exception_Authentication') $message = "Authentication failed.";
            return Payment_Response::instance()->gateway_response(
                'failure',
                $method.'_gateway_failure',
                (isset($message)) ? $message : $e->getMessage()
            );
        }

        return $this->_parse_response($response_raw);
    }

    /**
     * Maps PHP-Payments Methods to Details Particular to Each Request for that Method
     */
    public function method_map()
    {
        $map = array(
            'oneoff_payment' => array(
                'api' => 'Braintree_Transaction',
                'method' => 'sale',
                'required' => array(
                    'cc_number',
                    'cc_exp',
                    'amt',
                ),
                'keymatch' => array(
                    'amt' => 'amount',
                    'identifier' => 'orderId',
                    'cc_number' => 'creditCard["number"]',
                    'cc_exp' => 'creditCard["expirationDate"]',
                    'cc_code' => 'creditCard["cvv"]',
                    'first_name' => 'billing["firstName"]',
                    'last_name' => 'billing["lastName"]',
                    'street' => 'billing["streetAddress"]',
                    'street2' => 'billing["extendedAddress"]',
                    'postal_code' => 'billing["postalCode"]',
                    'state' => 'billing["region"]',
                    'country' => 'billing["countryCodeAlpha2"]',
                    'city' => 'billing["locality"]',
                    'ship_to_first_name' => 'shipping["firstName"]',
                    'ship_to_last_name' => 'shipping["lastName"]',
                    'ship_to_company' => 'shipping["company"]',
                    'ship_to_street' => 'shipping["streetAddress"]',
                    'ship_to_city' => 'shipping["locality"]',
                    'ship_to_state' => 'shipping["state"]',
                    'ship_to_postal_code' => 'shipping["postalCode"]',
                    'ship_to_country' => 'shipping["countryCodeAlpha2"]'
                ),
                'static' => array(
                    'options' => array(
                        'submitForSettlement' => true
                    )
                )
            ),
            'authorize_payment' => array(
                'api' => 'Braintree_Transaction',
                'method' => 'sale',
                'required' => array(
                    'cc_number',
                    'cc_exp',
                    'amt',
                ),
                'keymatch' => array(
                    'amt' => 'amount',
                    'identifier' => 'orderId',
                    'cc_number' => 'creditCard["number"]',
                    'cc_exp' => 'creditCard["expirationDate"]',
                    'cc_code' => 'creditCard["cvv"]',
                    'first_name' => 'billing["firstName"]',
                    'last_name' => 'billing["lastName"]',
                    'street' => 'billing["streetAddress"]',
                    'street2' => 'billing["extendedAddress"]',
                    'postal_code' => 'billing["postalCode"]',
                    'state' => 'billing["region"]',
                    'country' => 'billing["countryCodeAlpha2"]',
                    'city' => 'billing["locality"]',
                    'ship_to_first_name' => 'shipping["firstName"]',
                    'ship_to_last_name' => 'shipping["lastName"]',
                    'ship_to_company' => 'shipping["company"]',
                    'ship_to_street' => 'shipping["streetAddress"]',
                    'ship_to_city' => 'shipping["locality"]',
                    'ship_to_state' => 'shipping["state"]',
                    'ship_to_postal_code' => 'shipping["postalCode"]',
                    'ship_to_country' => 'shipping["countryCodeAlpha2"]'
                ),
                'static' => array(
                    'options' => array(
                        'submitForSettlement' => false
                    )
                )
            ),
            'capture_payment' => array(
                'api' => 'Braintree_Transaction',
                'method' => 'submitForSettlement',
                'required' => array(
                    'identifier'
                ),
                'keymatch' => array(
                    'identifier' => 'id'
                )
            ),
            'get_transaction_details' => array(
                'api' => 'Braintree_Transaction',
                'method' => 'find',
                'required' => array(
                    'identifier'
                ),
                'keymatch' => array(
                    'identifier' => 'id'
                )
            ),
            'refund_payment' => array(
                'api' => 'Braintree_Transaction',
                'method' => 'refund',
                'required' => array(
                    'identifier'
                ),
                'keymatch' => array(
                    'identifier' => 'id',
                    'amt' => 'amount'
                )
            ),
            'recurring_payment' => array(
                'api' => 'Braintree_Subscription',
                'method' => 'create',
                'required' => array(
                    'profile_reference',
                    'identifier'
                ),
                'keymatch' => array(
                    'profile_reference' => 'planId',
                    'identifier' => 'paymentMethodToken',
                    'amt' => 'price',
                    'trial_billing_cycles' => 'trialDuration',
                    'trial_billing_frequency' => 'trialDurationUnit',
                    'inv_num' => 'id'
                )
            ),
            'cancel_recurring_profile' => array(
                'api' => 'Braintree_Subscription',
                'method' => 'cancel',
                'required' => array(
                    'identifier'
                ),
                'keymatch' => array(
                    'identifier' => 'id'
                )
            ),
            'customer_create' => array(
                'api' => 'Braintree_Customer',
                'method' => 'create',
                'required' => array(
                    'first_name'
                ),
                'keymatch' => array(
                    'first_name' => 'firstName',
                    'last_name' => 'lastName',
                    'business_name' => 'company',
                    'email' => 'email',
                    'phone' => 'phone',
                    'fax' => 'fax',
                    'identifier' => 'id',
                    'cc_number' => 'creditCard["number"]',
                    'cc_exp' => 'creditCard["expirationDate"]',
                    'cc_code' => 'creditCard["cvv"]'
                )
            ),
            'token_create' => array(
                'api' => 'Braintree_CreditCard',
                'method' => 'create',
                'required' => array(
                    'custom',
                    'cc_number',
                    'cc_exp'
                ),
                'keymatch' => array(
                    'custom' => 'customerId',
                    'cc_number' => 'number',
                    'cc_exp' => 'expirationDate',
                    'cc_code' => 'cvv'
                )
            )
        );
        return $map;
    }

    /**
     * Builds the Request
     */
    protected function _build_request($params)
    {
        $api = $this->_api;

        $method = $this->_api_method;

        $params_ready = $this->_match_params($params);

        //Now we have what we need to build the request
        return array($api, $method, $params_ready);
    }

    /**
     * Match the Params
     */
    private function _match_params($params)
    {
        $map = $this->method_map();
        $l = $this->_lib_method;
        $params_ready = array();

        if(isset($map[$l]['keymatch'])) {

            $matcher = $map[$l]['keymatch'];

            foreach($params as $k=>$v)
            {
                if(isset($matcher[$k]))
                {
                        $val = ($k === 'cc_exp' ? $this->_format_date($v) : $v);

                    if(strpos($matcher[$k], '["') !== false)
                    {
                        $ex = explode('["', $matcher[$k]);
                        $arr = $ex[0];
                        $arrk = str_replace('"]', '', $ex[1]);

                        if(!isset($params_ready[$arr])) $params_ready[$arr] = array();

                            $params_ready[$arr][$arrk] = $val;

                        }
                        else
                        {
                        $key = $matcher[$k];

                        $params_ready[$key] = $val;
                    }
                }
                else
                {
                    error_log("$k is not a valid param for this method in this driver");
                }
            }

        }

        if(isset($map[$l]['static']))
        {
            $static = $map[$l]['static'];

            foreach($static as $k=>$v)
            {
                $params_ready[$k] = $v;
            }
        }

        return $params_ready;
    }

    /**
     * Convert the date to the Braintree accepted format
     *
     * @param $date
     * @return string
     */
    private function _format_date($date) {

        $d_mm = substr($date, 0, 2);
        $d_yyyy = substr($date, 2, 4);

        $formatedDate = $d_mm.'/'.$d_yyyy; //set the date

        return $formatedDate;
    }

    /**
     * Parse the response from the server
     *
     * @param	array
     * @return	object
     */
    protected function _parse_response($response)
    {
        $requestDetails = null;
        foreach($response as $prop) {
            if(is_object($prop) && get_class($prop)==$this->_api)
                $requestDetails = $prop;
        }

        $details = (object) array();
        $details->gateway_response = $response;

        $details->identifier = null;
        if( isset($requestDetails->id) ) $details->identifier = $requestDetails->__get('id');

        switch ($this->_api) {
            case 'Braintree_Transaction':
                if( isset($requestDetails->processorResponseText) ) $details->reason = $requestDetails->__get('processorResponseText');
            case 'Braintree_Customer':
                if( isset($requestDetails->createdAt) ) $details->timestamp = $requestDetails->__get('createdAt');
                break;
        }

        if ($response->success === true) {
            $indicator = 'success';
            $paymentResponse = $this->_lib_method.'_'.$indicator;
        }
        else
        {
            $indicator = 'failure';
            $paymentResponse = $this->_lib_method.'_gateway_'.$indicator;
        }


        return Payment_Response::instance()->gateway_response(
            $indicator,
            $paymentResponse,
            $details
        );
    }
}
