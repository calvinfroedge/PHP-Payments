<?php

class Void_Payment implements Payment_Method
{
	private $_params;

	public function __construct()
	{
		$this->_params = array(
			'identifier'			=>	'ID2930238',	//Required. Unique identifier for the transaction, generated from a previous authorization.
			'note'					=>	'Some note to tell why you voided it.' //An optional note to be submitted along with the request.
		);
	}

	public function get_params()
	{
		return $this->_params;
	}
}