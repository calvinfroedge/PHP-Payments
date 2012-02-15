<?php

class Recurring_Bill_Outstanding implements Payment_Method
{
	private $_params;

	public function __construct()
	{
		$this->_params = array(
			'identifier'	=> '', //Required.  Should have been returned when you created the profile.
			'amt'			=>	'', //The outstanding amount to bil.  Cannot exceed total owed.
			'note'			=> '' //This is just a note.
		);
	}

	public function get_params()
	{
		return $this->_params;
	}
}