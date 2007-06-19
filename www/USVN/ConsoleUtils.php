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
class USVN_ConsoleUtils
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

	private static $lang;
	private static $lc_ctype;

	static private function prepareLang()
	{
		USVN_ConsoleUtils::$lang = getenv('LANG');
		putenv('LANG=en_US.utf8');
		USVN_ConsoleUtils::$lc_ctype = getenv('LC_CTPYE');
		putenv('LC_CTYPE=UTF-8');
	}

	static private function restoreLang()
	{
		putenv('LANG=' . USVN_ConsoleUtils::$lang);
		putenv('LC_CTYPE=' . USVN_ConsoleUtils::$lc_ctype);
	}

	/**
	* Run a cmd and return result from stdout and stderror
	*
	* @param string command line
	* @param reference return value
	* @return string Ouput of STDOUT and STDERR
	*/
	static public function runCmdCaptureMessage($command, &$return)
	{
		USVN_ConsoleUtils::prepareLang();
		ob_start();
		passthru($command . " 2>&1", $return);
		$msg = ob_get_contents();
		ob_end_clean();
		USVN_ConsoleUtils::restoreLang();
		return(htmlspecialchars($msg));
	}

	/**
	* Run a cmd and return result
	*
	* @param string command line
	* @return int programm return code
	*/
	static public function runCmd($command)
	{
		USVN_ConsoleUtils::prepareLang();
		ob_start();
		system($command . " 2>&1", $return);
		ob_end_clean();
		USVN_ConsoleUtils::restoreLang();
		return($return);
	}
}
