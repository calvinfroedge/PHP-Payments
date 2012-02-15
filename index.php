<?php

require_once 'lib/payments.php';

$p = new Payments(array('test_mode' => true));

$config = Payment_Utility::load('config', 'drivers/paypal_paymentspro');

var_dump(
	$p->oneoff_payment('paypal_paymentspro', 
		array(
			'cc_number' => '4997662409617853',
			'cc_code' => '203',
			'cc_type' => 'Visa',
			'cc_exp' => '022016',
			'amt' => '10.00',
			'currency_code' => 'usd'
		),
		$config
	)
);
