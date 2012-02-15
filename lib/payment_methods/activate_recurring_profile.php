<?php

class Activate_Recurring_Profile implements Payment_Method
{
	private $_params;

	public function __construct()
	{
		$this->_params = array(
			'identifier'	=>	'', //Required.  Should have been returned when you created the profile.
			'note'			=>	'', //This is just a note.
		);
	}

	public function get_params()
	{
		return $this->_params;
	}
}