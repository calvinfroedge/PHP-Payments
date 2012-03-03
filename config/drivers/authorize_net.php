<?php

$config['api_username'] = "25xTC7vJp";
$config['api_password'] = "3zF64K5BA9a3y6rG";
//Note: you can sign into the test interface at test.authorize.net
$config['api_endpoint_test'] = "https://apitest.authorize.net/xml/v1/request.api";
$config['api_endpoint_production'] = "https://secure.authorize.net/gateway/transact.dll";
$config['email_customer'] = TRUE;
$config['test_mode'] = FALSE; //If using a test account, don't worry about this.  If you are using a real account, putting test_mode in FALSE will result in card you run being charged.

return $config;
