<?php

class Get_Oneoff_Button implements Payment_Method
{
	private $_params;

	public function __construct()
	{
		$this->_params = array(
			'amt' => '25.00',
			'desc' => 'This is the button text'
		);
	}

	public function get_params()
	{
		return $this->_params;
	}
}