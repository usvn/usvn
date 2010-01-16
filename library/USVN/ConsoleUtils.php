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
 * $Id: ConsoleUtils.php 1188 2007-10-06 12:03:17Z crivis_s $
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

	private static $lang = null;
	private static $locale = null;

	static public function setLocale($system_locale)
	{
		USVN_ConsoleUtils::$locale = $system_locale;
	}

	static private function prepareLang()
	{
		if (USVN_ConsoleUtils::$locale !== null) {
			USVN_ConsoleUtils::$lang = getenv('LANG');
			putenv('LANG=' . USVN_ConsoleUtils::$locale);
		}
	}

	static private function restoreLang()
	{
		if (USVN_ConsoleUtils::$lang !== null) {
			putenv('LANG=' . USVN_ConsoleUtils::$lang);
		}
	}

	/**
	* Run a cmd and return result from stdout and stderror. Html characters are escaped.
	*
	* @param string command line
	* @param reference return value
	* @return string Ouput of STDOUT and STDERR
	*/
	static public function runCmdCaptureMessage($command, &$return)
	{
		return(htmlspecialchars(USVN_ConsoleUtils::runCmdCaptureMessageUnsafe($command, $return)));
	}

	/**
	* Run a cmd and return result from stdout and stderror. Unsafe doesn't escape HTML.
	*
	* @param string command line
	* @param reference return value
	* @return string Ouput of STDOUT and STDERR
	*/
	static public function runCmdCaptureMessageUnsafe($command, &$return)
	{
		USVN_ConsoleUtils::prepareLang();
		ob_start();
		passthru($command . " 2>&1", $return);
		$msg = ob_get_contents();
		ob_end_clean();
		USVN_ConsoleUtils::restoreLang();
		return($msg);
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
		$returnValue = 0;
		system($command . " 2>&1", $returnValue);
		ob_end_clean();
		USVN_ConsoleUtils::restoreLang();
		return($returnValue);
	}
}
