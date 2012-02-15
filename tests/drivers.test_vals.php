<?php

$vals = array(
	'all' => array(
		'cc_number' => '4111111111111111',
		'cc_code' => '203',
		'cc_type' => 'Visa',
		'cc_exp' => '022016',
		'amt' => '5.00',
		'first_name' => 'John',
		'last_name' => 'Doe',
		'email' => 'johndoe@gmail.com',
		'desc' => 'Testing',
		'profile_start_date' => date("Y-m-d"),
		'billing_period' => 'Month',
		'billing_frequency' => '1',
		'total_billing_cycles' => '9999',
		'country_code' => 'US',
		'currency_code' => 'usd',
		'phone' => '(239) 239 2392'
	),
	'authorize_net' => array(
		'cc_number' => '4997662409617853',
	)
);

return $vals;
