<?php

class Do_Express_Checkout_Method implements Payment_Method
{
	private $_params;

	private $_descrip = "Creates HTML code for a button which directs the user to a hosted payments page on which they can complete the transaction.  Similar to Oneoff_Payment.";

	public function __construct()
	{
		$this->_params = array(
			'token'			=>  'EC-6RV20511T7745502A',		// token received from paypal
			'payer_id'		=>  'Z98CWZC8PA9Y2',			// payer id received from paypal
			'amt'			=>	'10.00',					// Amount for the payment
			'currency_code' =>  'EUR',
			'ip_address' 	=>  '127.0.0.1'					// ip address of the payer
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