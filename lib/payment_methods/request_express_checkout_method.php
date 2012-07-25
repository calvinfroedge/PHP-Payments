<?php

class Request_Express_Checkout_Method implements Payment_Method
{
	private $_params;

	private $_descrip = "Creates HTML code for a button which directs the user to a hosted payments page on which they can complete the transaction.  Similar to Oneoff_Payment.";

	public function __construct()
	{
		$this->_params = array(
			'amt'			=>	'10.00',	//Amount for the payment
			'desc'			=>	'Your button text',
			'currency_code' =>  'EUR',
			'return_url'	=>	'http://returnto.me/url',	//Your URL for receiving Instant Payment Notification (IPN) about this transaction. If you do not specify this value in the request, the notification URL from your Merchant Profile is used, if one exists.
			'cancel_url'	=>	'http://cancel.purchase', //Link for continue shopping button
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