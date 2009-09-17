<?php
/**
 * Tools for USVN's template
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
 * $Id: Template.php 288 2007-04-06 23:40:57Z dolean_j $
 */

class USVN_Template
{
	static private $template;
	static private $locale_directory;

	/**
	* Set template use by USVN.
	*
	* @var string template's name (ex: fr_Fr)
	* @var string directory of locales
	*/
	public static function initTemplate($template, $locale_directory)
	{
		USVN_Template::$template = $template;
		USVN_Template::$locale_directory = $locale_directory;
	}

	/**
	* Return current template use by USVN.
	*
	* @return string template's name
	*/
	public static function getTemplate()
	{
		return USVN_Template::$template;
	}

	/**
	* Return current locale directory
	*
	* @return string directory of locales
	*/
	public static function getLocaleDirectory()
	{
		return USVN_Template::$locale_directory;
	}

	/**
	 * Return the validity of a directory
	 *
	 * @param string $directory
	 * @return bool
	 */
	public static function isValidTemplateDirectory($directory)
	{
		if (!is_dir($directory)) {
			return false;
		}
		if (!file_exists($directory . '/screen.css')) {
			return false;
		}
		if (!file_exists($directory . '/print.css')) {
			return false;
		}
		if (!file_exists($directory . '/script.js')) {
			return false;
		}
		return true;
	}

	/**
	* Return available templates
	*
	* @todo check if it is a valid template directory
	* @return array
	*/
	public static function listTemplate()
	{
		$res = array();
//		echo USVN_Template::$locale_directory . "<br />\n";
		$list = USVN_DirectoryUtils::listDirectory(USVN_Template::$locale_directory);
		foreach ($list as $filename) {
			if (USVN_Template::isValidTemplateDirectory(USVN_Template::$locale_directory . '/' . $filename)) {
				$res[] = $filename;
			}
		}
		return $res;
	}
}
