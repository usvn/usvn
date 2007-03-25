<?php
/**
 * Tools for translation
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

class USVN_Translation
{
	static private $language;
	static private $locale_directory;
	static private $translation_instance;

	/**
	* Set language use by PHP and gettext.
	*
	* @var string language code (ex: fr_Fr)
	* @var string directory of locales
	*/
	public static function initTranslation($language, $locale_directory)
	{
		USVN_Translation::$language = $language;
		USVN_Translation::$locale_directory = $locale_directory;
		USVN_Translation::$translation_instance = new Zend_Translate('gettext', "$locale_directory/$language/messages.mo", $language);
	}

	/**
	* Return current language use by PHP and gettext.
	*
	* @return string language code (ex: fr_Fr)
	*/
	public static function getLanguage()
	{
		return USVN_Translation::$language;
	}

	/**
	* Return current locale directory
	*
	* @return string directory of locales
	*/
	public static function getLocaleDirectory()
	{
		return USVN_Translation::$locale_directory;
	}

	/**
	* Return available translations
	*
	* @return array
	*/
	public static function listTranslation()
	{
		$res = array();
		$dh = opendir(USVN_Translation::$locale_directory);
		while (($subDir = readdir($dh)) !== false) {
            if ($subDir != '.' && $subDir != '..' && $subDir != '.svn') {
				array_push($res, $subDir);
			}
        }
		return $res;
	}

	/**
	* Translate the given string
	*
	* @param string
	* @return string
	*/
	public static function  _($str)
	{
		return USVN_Translation::$translation_instance->_($str);
	}
}
