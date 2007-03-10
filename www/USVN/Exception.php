<?php
require_once 'Zend/Exception.php';

/**
 * @category   USVN
 * @package    USVN
 */
class USVN_Exception extends Zend_Exception {
	public function __construct($message)
	{
		$params = func_get_args();
		$params[0] = T_($message);
		$message = call_user_func_array("sprintf", $params);
		parent::__construct($message);
	}
}