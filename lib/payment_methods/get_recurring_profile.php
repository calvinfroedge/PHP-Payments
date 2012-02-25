<?php

class Get_Recurring_Profile implements Payment_Method
{
	private $_params;

	public function __construct()
	{
		$this->_params = array(
			'identifier'	=>	'PROFILE-2923849', //Required.  Should have been returned when you created the profile.
		);
	}

	public function get_params()
	{
		return $this->_params;
	}
}