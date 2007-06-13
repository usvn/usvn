<?php
/**
 * Base classe for exception into USVN
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package usvn
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

class USVN_Exception extends Zend_Exception {
	/**
	* @param string
	*/
	public function __construct($message)
	{
		if (func_num_args() > 1) {
			$params = func_get_args();
			$params[0] = $message;
			$message = call_user_func_array("sprintf", $params);
		}
		parent::__construct($message);
	}
}
