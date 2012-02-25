<?php

class Change_Transaction_Status implements Payment_Method
{
	private $_params;

	public function __construct()
	{
		$this->_params = array(
			'identifier'			=>	'TRANS-239239',  //Required. Unique identifier for the transaction, generated from a previous transaction.
			'action'				=>	'Accept'  //Required.  Should be Accept or Deny.
		);
	}

	public function get_params()
	{
		return $this->_params;
	}
}