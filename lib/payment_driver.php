<?php

abstract class Payment_Driver 
{
	abstract public function __construct($config);

	/**
	 * Call Magic Method Must Be Implemented
	*/
	abstract public function __call($method, $params);

	/**
	 * Maps Methods to Details Particular to Each Request for that Method
	 */
	abstract public function method_map();

	/**
	 * Builds the Request
	 */
	abstract protected function _build_request($params);

	/**
	 * Parse the Response and then Delegate to the Response Object
	 */
	abstract protected function _parse_response($response);
}
