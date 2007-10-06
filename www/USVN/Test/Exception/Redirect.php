<?php
/**
 * Base classe for exception into USVN
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.7
 * @package test
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id: Exception.php 756 2007-06-13 09:27:51Z crivis_s $
 */

class USVN_Test_Exception_Redirect extends Exception {
	public $url;
	public $options;

	/**
	* @param string $url
	* @param array $options Options to be used when redirecting
	*/
	public function __construct($url, array $options = array())
	{
		$this->url = $url;
		$this->options =$options;
		parent::__construct($url);
	}
}
