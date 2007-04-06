<?php
/**
 * Create an svn repository and install usvn hooks.
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package client
 * @subpackage create
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
class USVN_Client_Create extends USVN_Client_Install
{
	/**
	* @param string Local path of svn repository
	* @param string Url of USVN
	* @param string Auth id
	* @param Zend_Http_Client HTTP client for XML don't change this except for tests
	*/
    public function __construct($path, $url, $authid, $httpclient = NULL)
    {
		$msg = USVN_Client_ConsoleUtils::runCmdCaptureMessage("svnadmin create " . $path, $return);
		if ($return != 0) {
			throw new USVN_Exception("Can't create svn repository into $path\n" . $msg);
		}
		try {
			parent::__construct($path, $url, $authid, $httpclient);
		}
		catch (USVN_Exception $e) {
			USVN_DirectoryUtils::removeDirectory($path);
			throw $e;
		}
	}
}
