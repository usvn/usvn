<?php

set_include_path(dirname(__FILE__) . '/../' . PATH_SEPARATOR . get_include_path());

require_once 'Zend.php';

/**
 * Autoload a class when requested.
 *
 * This is a PHP magic function which is call
 * when a script use a class that does not exist.
 *
 * @param string $class
 */
function __autoload($class)
{
	Zend::loadClass($class);
}

