<?php

class Refund_Payment implements Payment_Method
{
	private $_params;

	public function __construct()
	{
		$this->_params = array(
			'identifier'			=>	'TRANS-ID', //A unique identifier for the transaction
			'inv_num'				=>	'INV-2392329',
			'refund_type'			=>	'Full', //Can be Full or Partial
			'amt'					=>	'',  //Do not set amount if refund type is Full
			'currency_code'			=>	'USD',
			'note'					=>	'This is a note to send with the refund.',
			'last_4_digits'			=>	'4111', //Last 4 digits of the credit card used		
		);
	}

	public function get_params()
	{
		return $this->_params;
	}
}