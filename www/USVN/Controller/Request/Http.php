<?php
/**
 * Class for HTTP request
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
class USVN_Controller_Request_Http extends Zend_Controller_Request_Http
{
	/**
     * Retrieve the controller name
     *
     * @return string
     */
	public function getControllerName()
	{
		if ($this->getParam('area') == "admin") {
			return $this->_controller . "admin";
		}
		return $this->_controller;
	}
}
