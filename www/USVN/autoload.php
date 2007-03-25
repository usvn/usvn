<?php

set_include_path(dirname(__FILE__) . '/../' . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Loader.php';
spl_autoload_register(array('Zend_Loader', 'autoload'));

function T_($str)
{
	return USVN_Translation::_($str);
}
