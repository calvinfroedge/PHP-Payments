<?php

$dir = dirname(__DIR__);
$drivers = scandir($dir."/lib/payment_drivers");
include($dir."/lib/payments.php");

$p = new Payments;

$config_values = include('drivers.test_vals.php');

class errors
{
	public static $errors = array();

	static function set_error($class, $method, $code, $message, $details)
	{
		$error = array(
			'class' => $class,
			'method' => $method,
			'response_code' => $code,
			'response_message' => $message,
			'details' => $details
		);

		self::$errors[] = (object) $error;
	}
}

$count = 0;
foreach($drivers as $driver)
{
	//Reset the identifier
	$last_identifier = '';

	//Ignore names that start with "."
	if($driver[0] !== '.')
	{
		$class_name = str_replace('.php', '', $driver);
		$config_name = str_replace('_driver', '', $class_name);
		$config = Payment_Utility::load('config', 'drivers/'.$config_name);
		$class = new $class_name($config);

		$methods_available = $class->method_map();

		foreach($methods_available as $method=>$method_array)
		{
			$required = $method_array['required'];

			$args = array();

			$break = false;
			foreach($required as $r)
			{
				if(isset($config_values[$class_name][$r])) 
				{
					$args[$r] = $config_values[$class_name][$r];
				}
				else if(isset($config_values['all'][$r]))
				{
					$args[$r] = $config_values['all'][$r];
				}

				if($r == 'identifier')
				{
					if(empty($last_identifier))
					{
						errors::set_error($class_name, $method, '000', 'An identifier was not retrieved in the previous transaction, but is required for '.$method.', so failure is certain.', 'No further details available.');
						$break = true;
					}
				}
			}

			//If an error was already found, don't bother calling the method
			if($break == true) { continue;}
			
			//Make the call to the method
			$result = $class->$method($args);

			if($result->response_code != 100)
			{
				errors::set_error($class_name, $method, $result->response_code, $result->response_message, $result->details);
			}

			if(isset($result->details->identifier))
			{
				$last_identifier = $result->details->identifier;
			}
		}
	}
	++$count;
}

if(count(errors::$errors) > 0)
{
	echo "Your test resulted in the following errors: \n \n";

	$e = errors::$errors;

	foreach($e as $k=>$v)
	{
		echo "Attempt to perform the ".$e[$k]->method." method on class ".$e[$k]->class." was unsuccessful.  The following details may help you in debugging this: \n \n";
	
		echo "The response code: ".$e[$k]->response_code." \n \n";
	
		echo "The response message: ".$e[$k]->response_message." \n \n";

		echo "The details of the response: \n \n";

		var_dump($e[$k]->details);

		echo " \n \n";

		if(isset($e[$k]->errors_warnings)) echo "Additionally, the following errors and warnings were observed:";

		echo " \n \n";
	}
}

echo "Thankyou for choosing PHP-Payments! Please send questions, comments or donations (via PayPal) to calvinfroedge@gmail.com.  If you find a bug, please post it in the issues section of the Git repository: https://github.com/calvinfroedge/PHP-Payments  \n \n";
