<?php

class Customer_Create_Method implements Payment_Method
{
	private $_params;

	private $_descrip = "Create a customer instance which is stored in the gateway.";

	public function __construct()
	{
		$this->_params = array(
            'first_name'		=>	'Calvin', //first name of the customer
            'last_name'			=>	'Froedge', //last name of the customer
            'email'				=>	'calvinsemail@gmail.com', //email of the customer
            'business_name'		=>	'The Business Name', //name of business
            'phone'				=>	'(801) 754 4466', //phone num of customer
            'fax'				=>	'(801) 754 4466',
			'desc'			    =>  'Some description',
			'identifier'	    =>	'PROFILE-2923849', //Custom identifier for the customer being created
            'cc_type'			=>	'Visa',	//Visa, MasterCard, Discover, Amex
            'cc_number'			=>	'4111111111111111', //Credit card number
            'cc_exp'			=>	'022013', //Must be formatted MMYYYY
            'cc_code'			=>	'413' //3 or 4 digit cvv code
		);
	}

	public function get_params()
	{
		return $this->_params;
	}

	public function get_description()
	{
		return $this->_descrip();
	}
}
