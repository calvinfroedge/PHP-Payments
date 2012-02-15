<?php

class Get_Transaction_Details implements Payment_Method
{
	private $_params;

	public function __construct()
	{
		$this->_params = array(
			'identifier'	=>	'', //Required.  Should have been returned when you created the transaction.
		);
	}

	public function get_params()
	{
		return $this->_params;
	}
}