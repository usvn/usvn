<?php
/**
 * Class with usefull static method for console.
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package client
 * @subpackage console
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
class USVN_Client_ConsoleUtils
{
	/**
	* @param string Write the given string on stderror.
	*/
	static public function writeOnStdError($str)
	{
		$stderr = fopen('php://stderr', 'w');
		fwrite($stderr, $str);
		fclose($stderr);
	}
}
