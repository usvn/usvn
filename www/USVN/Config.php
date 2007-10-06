<?php
/**
 * Model for configuration pages
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package admin
 * @subpackage config
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

class USVN_Config
{
	/**
	 * Set default language in the config file
	 *
	 * @param string The default language
	 * @throw USVN_Exception
	 */
	static public function setLanguage($language)
	{
		if (in_array($language, USVN_Translation::listTranslation())) {
			$config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
			$config->translation->locale = $language;
			$config->save();
		} else {
			throw new USVN_Exception(T_("Invalid language"));
		}
	}

	/**
	 * Set default time zone in the config file
	 *
	 * @param string The default time zone
	 * @throw USVN_Exception
	 */
	static public function setTimeZone($timezone)
	{
		$availableTimezones = Zend_Locale_Data::getContent("en", "timezonestandard");
		if (array_key_exists($timezone, $availableTimezones)) {
			$config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
			$config->timezone = $timezone;
			$config->save();
		}
		else {
			throw new USVN_Exception(T_("Invalid timezone"));
		}
	}

	static public function setTemplate($template)
	{
		if (in_array($template, USVN_Template::listTemplate())) {
			$config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
			$config->template->name = $template;
			$config->save();
		} else {
			throw new USVN_Exception(T_("Invalid template"));
		}
	}

	/**
	 * Set information about the Website
	 *
	 * @todo Check if the file exists or launch an exception
	 * @throw USVN_Exception
	 * @param array $datas
	 */
	static public function setSiteDatas($datas)
	{
		$config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
		$config->site->title = $datas['title'];
		$config->site->ico = $datas['ico'];
		$config->site->logo = $datas['logo'];
		$config->save();
	}
}
