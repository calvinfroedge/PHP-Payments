<?php

class PayPal_Expresscheckout_Driver extends Payment_Driver
{	
	/**
	 * The PHP Payments Method
	*/
	private $_lib_method;

	/**
	 * The Paypal API to use
	*/
	private $_api;

	/**
	 * Test or Production Mode
	*/
	private $_mode;

	/**
	 * The API endpoint to send the request to
	*/
	private $_api_endpoint = 'https://api-3t.paypal.com/nvp';

	/**
	 * The API endpoint to send a test request to
	*/
	private $_api_endpoint_test = 'https://api-3t.sandbox.paypal.com/nvp';

	/**
	 * Current Version of PayPal's API
	*/
	private $_api_version = '66.0';

/**
	 * An array for storing all settings
	*/	
	private $_settings = array();

	/**
	 * Constructor method
	*/		
	public function __construct($config)
	{	
		$this->_settings = array(
			'USER'	=> $config['api_username'],
			'PWD'	=> $config['api_password'],
			'SIGNATURE'	=> $config['api_signature'],
			'VERSION' => $this->_api_version
		);
		$this->_mode = $config['mode'];
	}

	/**
	 * Caller Magic Method
	*/
	public function __call($method, $params)
	{
		$this->_lib_method = $method;
	
		$args = $params[0];
		$request = $this->_build_request($args);
		$endpoint = ($this->_mode == 'production') ? $this->_api_endpoint : $this->_api_endpoint_test;
		$request_string = $endpoint.'?'.$request;

		$raw_response = Payment_Request::curl_request($request_string);
		return $this->_parse_response($raw_response);
	}

	/**
	 * Maps Methods to Keys
	*/ 
	public function method_map()
	{
		$map = array(
			'request_express_checkout' => array(
				'api' => 'SetExpressCheckout',
				'required' => array(
					'amt',
					'currency_code',
					'return_url',
					'cancel_url'
				),
				'keymatch' => array(
					'amt' => 'AMT',
					'currency_code'	=> 'CURRENCYCODE',
					'return_url' => 'RETURNURL',
					'cancel_url' => 'CANCELURL'
				),
				'static' => array(
					'PAYMENTACTION'	=>	'Sale'
				)
			),
			'do_express_checkout' =>	array(
				'api' => 'DoExpressCheckoutPayment',
				'required' => array(
					'token',  //Reference for a previous payment
					'payer_id',
					'amt',
					'ip_address'
				),
				'keymatch' => array(
					'token' => 'TOKEN',
					'payer_id' => 'PAYERID',
					'currency_code' => 'CURRENCYCODE',
					'amt' => 'AMT',
					'ip_address' => 'IPADDRESS'
				),
				'static' => array(
					'PAYMENTACTION'	=>	'Sale'
				)
			),
			'void_payment'	=>	array(
				'api'	=>	'DoVoid',
				'required'	=>	array(
					'identifier',
					'note'
				),
				'keymatch' => array(
					'identifier'	=>	'AUTHORIZATIONID',
					'note'			=>	'NOTE'
				)
			)
		);
		return $map;
	}	

	/**
	 * Build the Request
	 *
	 * @param	array
	 * @return	array
	*/
	protected function _build_request($params)
	{
		//Normalize some param formats
		$params_adjusted = array();
		foreach($params as $k=>$v)
		{
			if($k == 'currency_code')
			{
				$val = strtoupper($v);
			}
			else
			{
				$val = $v;
			}

			$params_adjusted[$k] = $val;
		}

		$args = $this->_match_params($params_adjusted);
		$request = http_build_query(array_merge(array('METHOD' => $this->_api), $this->_settings, $args));
		return $request;
	}

	/**
	 * Match Params
	 *
	 * @param array
	 * @return	array
	*/
	private function _match_params($params)
	{
		$l = $this->_lib_method;
		$map = $this->method_map();
		$this->_api = $map[$l]['api'];
	
		$fields = array();
		foreach($params as $k=>$v)
		{
			if(isset($map[$l]['keymatch'][$k]))
			{
				$key = $map[$l]['keymatch'][$k];
				if(!isset($fields[$key]))
				{
					$fields[$key] = $v;
				}
				else
				{
					$fields[$key] .= " $v";
				}
			}
			else
			{
				error_log("$k is not a valid param for the $l method of the Paypal PaymentsPro driver.");
			}
		}
		
		if(isset($map[$l]['static']))
		{
			$static = $map[$l]['static'];
	
			foreach($static as $k=>$v)
			{
				$fields[$k] = $v;
			}
		}

		return $fields;
	}

	/**
	 * Parse the response from the server
	 *
	 * @param	array
	 * @return	object
	 */		
	protected function _parse_response($response)
	{	
	
		if($response === FALSE)
		{
			return Payment_Response::instance()->gateway_response(
				'Failure',
				$this->_lib_method.'_gateway_failure'
			);			
		}
		
		$results = explode('&',urldecode($response));
		foreach($results as $result)
		{
			list($key, $value) = explode('=', $result);
			$gateway_response[$key]=$value;
		}
	
		$details = (object) array(
			'gateway_response' => (object) array()
		);
		foreach($gateway_response as $k=>$v)
		{
			$details->gateway_response->$k = $v;
		}

		if(isset($gateway_response['L_LONGMESSAGE0']))
		{
			$details->reason  =	$gateway_response['L_LONGMESSAGE0'];
		}

		if(isset($gateway_response['TIMESTAMP']))
		{
			$details->timestamp = $gateway_response['TIMESTAMP'];
		}
			
		if(isset($gateway_response['TRANSACTIONID']))
		{
			$details->identifier = $gateway_response['TRANSACTIONID'];
		}
			
		if(isset($gateway_response['PROFILEID']))
		{
			$details->identifier = $gateway_response['PROFILEID'];
		}				
			
		if($gateway_response['ACK'] == 'Success')
		{	
			return Payment_Response::instance()->gateway_response(
				'Success',
				$this->_lib_method.'_success',
				$details
			);
		}
		else
		{
			return Payment_Response::instance()->gateway_response(
				'Failure',
				$this->_lib_method.'_gateway_failure', 
				$details
			);		
		}	
	}	
}
