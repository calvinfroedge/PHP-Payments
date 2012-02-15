<?php

class Void_Refund implements Payment_Method
{
	private $_params;

	public function __construct()
	{
		$this->_params = array(
			'cc_number' => '',
			'cc_type' 	=> '',
			'cc_code' 	=> '',
			'cc_exp'	=> '',
			'amt'		=> '',
			'first_name' => '',
			'last_name' => '',
			'phone' => '',
			'email' => '',
			'street' => '',
			'city' => '',
			'state' => '',
			'country' => '',
			'postal_code' => '',
			'identifier' => ''	
		);
	}

	public function get_params()
	{
		return $this->_params;
	}
}