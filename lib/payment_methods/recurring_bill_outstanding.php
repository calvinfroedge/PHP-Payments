<?php

class Recurring_Bill_Outstanding implements Payment_Method
{
	private $_params;

	public function __construct()
	{
		$this->_params = array(
			'identifier'	=> 'PROFILE-IDENTIFIER', //Required.  Should have been returned when you created the profile.
			'amt'			=> '35.00', //The outstanding amount to bil.  Cannot exceed total owed.
			'note'			=> 'Why the profile is being billed' //This is just a note.
		);
	}

	public function get_params()
	{
		return $this->_params;
	}
}