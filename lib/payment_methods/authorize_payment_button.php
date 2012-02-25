<?php

class Authorize_Payment_Button implements Payment_Method
{
	private $_params;

	public function __construct()
	{
		$this->_params = array(
			'amt'		=>	'2.00',	//Amount for the payment
			'desc'		=>	'Click here to buy me', //A description for the button
		);
	}

	public function get_params()
	{
		return $this->_params;
	}
}