<?php

class Authorize_Payment_Button implements Payment_Method
{
	private $_params;

	public function __construct()
	{
		$this->_params = array(
			'amt'		=>	'',	//Amount for the payment
			'desc'		=>	'', //A description for the transaction
		);
	}

	public function get_params()
	{
		return $this->_params;
	}
}