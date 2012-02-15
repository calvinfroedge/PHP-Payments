<?php

class Payment_Utility 
{
	public function __construct(){}

	public function class_autoload($class)
	{
		$class = strtolower($class);
		$base_dir = __DIR__.'/';

		if(file_exists($base_dir.$class.'.php'))
		{
			include_once($base_dir.$class.'.php');
			return;
		}
		else if(file_exists($base_dir.'payment_drivers/'.$class.'.php'))
		{
			include_once($base_dir.'payment_drivers/'.$class.'.php');
			return;
		}
		else if(file_exists($base_dir.'payment_methods/'.$class.'.php'))
		{
			include_once($base_dir.'payment_methods/'.$class.'.php');
			return;
		}
		else
		{
			throw new Exception("Could not find class");
		}
	}

	/**
	  * Load a resource.  Alternative to include / require / etc.
	*/
	public static function load($type, $file, $key = null)
	{
		$base_dir = dirname(__DIR__);

		switch($type)
		{
			case $type == 'config':
				$ob = false;
				$path = $base_dir.'/config/'.$file.'.php';
				break;

			case $type == 'file':
				$ob = true;
				$path = $base_dir.'/'.$file.'.php';
				break;
			
			case $type == 'lang':
				$ob = false;
				$path = $base_dir.'/language/'.$file.'_lang.php';
				break;

			default:
				die("$type is not a valid filetype to load for Payments");
		}

		if(!is_file($path)) die("$path does not exist.");

		if($ob)
		{
			ob_start();
				include_once($path);
			return ob_get_clean();	
		}
		else
		{
			$f = include $path;
			return (isset($f[$key])) ? $f[$key] : $f;
		}
	}

	/**
	 * Remove key=>value pairs with empty values
	 *
	 * @param	array	array of key=>value pairs to check
	 * @return	array	Will return filtered array
	 */
	public static function filter_values($array)
	{	
		foreach($array as $k=>$v)
		{
			$v = trim($v);
			if(empty($v) AND !is_numeric($v))
			{
				unset($array[$k]);
			}
		}
		return $array;
	}


	/**
	 * Arrayize an object
	 *
	 * @param	object	the object to convert to an array
	 * @return	array	a converted array
	*/
	public static function arrayize_object($input)
	{
		if(!is_object($input))
		{
			return $input;
		}
		else
		{
			$final = array();
			$vars = get_object_vars($input);
			foreach($vars as $k=>$v)
			{
				if(is_object($v))
				{
					$final[$k] = self::arrayize_object($v);
				}
				else
				{
					$final[$k] = $v;
				}
			}
		}
	
		return $final;
	}

	/**
	 * Parses an XML response and creates an object using SimpleXML
	 *
	 * @param 	string	raw xml string
	 * @return	object	response SimpleXMLElement object
	*/		
	public static function parse_xml($xml_str)
	{
		$xml_str = trim($xml_str);
		$xml_str = preg_replace('/xmlns="(.+?)"/', '', $xml_str);
		if($xml_str[0] != '<')
		{
			$xml_str = explode('<', $xml_str);
			unset($xml_str[0]);
			$xml_str = '<'.implode('<', $xml_str);
		}
		
		try {
			$xml = @new SimpleXMLElement($xml_str);
		}
		catch(Exception $e) {
			return Payment_Response::instance()->local_response(
				'failure',
				'invalid_xml',
				$xml_str
			);
		}

		return $xml;
	}


	/**
	 * Connection is Secure
	 * 
	 * Checks whether current connection is secure and will redirect
	 * to secure version of page if 'force_secure_connection' is TRUE
	 * 
	 * To Force HTTPS for your entire website, use a .htaccess like the following:
	 *
	 *  RewriteEngine On
	 *  RewriteCond %{SERVER_PORT} 80
	 *  RewriteRule ^(.*)$ https://domain.com/$1 [R,L]
	 * 
	 * @link http://davidwalsh.name/force-secure-ssl-htaccess
	 * @return	bool
	 */
	public static function connection_is_secure($config)
	{
		// Check whether secure connection is required
		if($config['force_secure_connection'] === FALSE) 
		{
			error_log('WARNING!! Using Payment Gateway without Secure Connection!', 0);
			return false;
		}
		
		// Redirect if NOT secure and forcing a secure connection.
		if(($_SERVER['SERVER_PORT'] === '443' && isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') === FALSE)
		{
			$loc = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			header($loc);
			exit;
		}
		
		return true;
	}
}
