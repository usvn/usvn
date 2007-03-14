<?php
/**
* Console utils
*
* @author Team USVN <contact@usvn.info>
* @link http://www.usvn.info
* @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
* @copyright Copyright 2007, Team USVN
* @since 0.5
* @package client
*
** This software has been written in EPITECH <http://www.epitech.net>
** EPITECH is computer science school in Paris - FRANCE -
** under the direction of Flavien Astraud <http://www.epita.fr/~flav>.
** and Jerome Landrieu.
**
** Project : USVN
** Date    : $Date: 2007-03-10 13:22:45 -0500 (sam, 10 mar 2007) $
*/


/**
* Class with usefull static method for console
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