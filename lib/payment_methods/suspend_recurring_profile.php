<?php

class Suspend_Recurring_Profile implements Payment_Method
{
	private $_params;

	public function __construct()
	{
		$this->_params = array(
			'identifier'	=>	'IDENTIFIER-2392032', //Required.  Should have been returned when you created the profile.
			'note'			=>	'This is the note', //This is just a note.
		);
	}

	public function get_params()
	{
		return $this->_params;
	}
}