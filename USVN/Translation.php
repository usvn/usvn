<?php
/**
* Tools for translatioon.
*
* @author Team USVN <contact@usvn.info>
* @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
* @copyright Copyright 2007, Team USVN
* @since 0.5
* @package usvn
*
** This software has been written in EPITECH <http://www.epitech.net>
** EPITECH is computer science school in Paris - FRANCE -
** under the direction of Flavien Astraud <http://www.epita.fr/~flav>.
** and Jerome Landrieu.
**
** Project : USVN
** Date    : $DATE$
*/

require_once('library/gettext/gettext.inc');

/**
*
*/
class USVN_Translation
{
	static private $language;
	static private $locale_directory;

	/**
	* Set language use by PHP and gettext.
	*
	* @var string language code (ex: fr_Fr)
	* @var string directory of locales
	*/
	static function initTranslation($language, $locale_directory = "locale/")
	{
		USVN_Translation::$language = $language;
		USVN_Translation::$locale_directory = $locale_directory;
		$encoding = 'UTF-8';
		T_setlocale(LC_MESSAGES, $language.'.'.$encoding);
		putenv("LANG=$language.$encoding");
		putenv("LANGUAGE=$language.$encoding");
		bindtextdomain("messages", USVN_Translation::$locale_directory);
		bind_textdomain_codeset("messages", "UTF-8");
		textdomain("messages");
	}

	/**
	* Return current language use by PHP and gettext.
	*
	* @return string language code (ex: fr_Fr)
	*/
	static function getLanguage()
	{
		return USVN_Translation::$language;
	}

	/**
	* Return current locale directory
	*
	* @return string directory of locales
	*/
	static function getLocaleDirectory()
	{
		return USVN_Translation::$locale_directory;
	}
}