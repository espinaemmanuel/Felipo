<?php
$config ['db'] = array (
		'driver' => 'sqlite',
		'type' => 'persistent',
		'directory' => 'db',
		'db' => 'felipo.sq3'
);

$config ['loginPage'] = '/login';

$config ['defaultModule'] = 'default';
$config ['anonymousAccess'] = false;
$config ['notRestrictedAccess'] = false;
$config ['timezone'] = date_default_timezone_get();

$config ['plugins'] = array (
		'db',
		'validador' 
);

$config ['external_js'] = array(
		'jquery' => 'http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js',
		'jqueryui' => 'http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.js',
		'prototype' => 'http://ajax.googleapis.com/ajax/libs/prototype/1/prototype.js',
		'scriptaculous' => 'http://ajax.googleapis.com/ajax/libs/scriptaculous/1/scriptaculous.js'		
		);

$config ['prefix'] = '';
$config ['logFile'] = 'log/error.log';
$config ['authFactory'] = 'AutentificadorBaseDatosFactory';

// AutentificadorBaseDatosFactory
// $config ['auth'] ['dbConn'] = 'default';

// AutentificadorConfigFactory
// $config ['auth'] ['users'] = array( 'admin' => 'admin' );

$config ['ssl'] = false;

$config ['smtp'] ['host'] = '';
$config ['smtp'] ['user'] = '';
