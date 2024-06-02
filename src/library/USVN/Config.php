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
 * $Id: Config.php 1519 2008-06-17 21:33:59Z duponc_j $
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
			$config->translation->locale = filter_var($language, FILTER_SANITIZE_STRING);
			$config->save();
		} 
		else {
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
		$availableTimezones = Zend_Locale_Data::getList("en", "WindowsToTimezone");
		if (array_key_exists($timezone, $availableTimezones)) {
			$config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
			$config->timezone = filter_var($timezone, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$config->save();
		}
		else {
			throw new USVN_Exception(T_("Invalid timezone"));
		}
	}

	/**
	* Change style of USVN
	*
	* @param string template name
	* @throw USVN_Exception
	*/
	static public function setTemplate($template)
	{
		if (in_array($template, USVN_Template::listTemplate())) {
			$config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
			$config->template->name = filter_var($template, FILTER_SANITIZE_STRING);
			$config->save();
		}
		else {
			throw new USVN_Exception(T_("Invalid template"));
		}
	}

	/**
	* Allow check of update or not
	*
	* @param bool
	*/
	static public function setCheckForUpdate($check)
	{
		$config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
		$config->update = array("checkforupdate" => filter_var($check, FILTER_SANITIZE_STRING), "lastcheckforupdate" => 0);
		$config->save();
	}

	/**
	 * Set information about the Website
	 *
	 * @throw USVN_Exception
	 * @param array $datas
	 */
	static public function setSiteDatas($datas)
	{
		$config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
		$config->site->title = filter_var($datas['title'], FILTER_SANITIZE_STRING);
		$config->site->ico = filter_var($datas['ico'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		$config->site->logo = filter_var($datas['logo'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		$config->save();
	}

	/**
	 * Set LDAP configuration
	 *
	 * @throw USVN_Exception
	 * @param array $ldap_options
	 */
	static public function setLDAPConfig($ldap_options)
	{
		$config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
		if (!$config->ldap)
		{
			$config->ldap = array();
		}
		if (!$config->ldap->options)
		{
			$config->ldap->options = array();
		}
		$config->ldap->createGroupForUserInDB = filter_var($ldap_options['createGroupForUserInDB'], FILTER_SANITIZE_NUMBER_INT);
		$config->ldap->createUserInDBOnLogin = filter_var($ldap_options['createUserInDBOnLogin'], FILTER_SANITIZE_NUMBER_INT);
		if (strlen($ldap_options['host'])) $config->ldap->options->host = filter_var($ldap_options['host'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		if (strlen($ldap_options['port'])) $config->ldap->options->port = filter_var($ldap_options['port'], FILTER_SANITIZE_NUMBER_INT);
		if (strlen($ldap_options['username'])) $config->ldap->options->username = filter_var($ldap_options['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		if (strlen($ldap_options['password'])) $config->ldap->options->password = filter_var($ldap_options['password'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		if (strlen($ldap_options['useStartTls'])) $config->ldap->options->useStartTls = filter_var($ldap_options['useStartTls'], FILTER_SANITIZE_NUMBER_INT);
		if (strlen($ldap_options['useSsl'])) $config->ldap->options->useSsl = filter_var($ldap_options['useSsl'], FILTER_SANITIZE_NUMBER_INT);
		if (strlen($ldap_options['bindDnFormat'])) $config->ldap->options->bindDnFormat = filter_var($ldap_options['bindDnFormat'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		if (strlen($ldap_options['bindRequiresDn'])) $config->ldap->options->bindRequiresDn = filter_var($ldap_options['bindRequiresDn'], FILTER_SANITIZE_NUMBER_INT);
		if (strlen($ldap_options['baseDn'])) $config->ldap->options->baseDn = filter_var($ldap_options['baseDn'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		if (strlen($ldap_options['accountCanonicalForm'])) $config->ldap->options->accountCanonicalForm = filter_var($ldap_options['accountCanonicalForm'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		if (strlen($ldap_options['accountDomainName'])) $config->ldap->options->accountDomainName = filter_var($ldap_options['accountDomainName'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		if (strlen($ldap_options['accountDomainNameShort'])) $config->ldap->options->accountDomainNameShort = filter_var($ldap_options['accountDomainNameShort'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		if (strlen($ldap_options['accountFilterFormat'])) $config->ldap->options->accountFilterFormat = filter_var($ldap_options['accountFilterFormat'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		if (strlen($ldap_options['allowEmptyPassword'])) $config->ldap->options->allowEmptyPassword = filter_var($ldap_options['allowEmptyPassword'], FILTER_SANITIZE_NUMBER_INT);
		if (strlen($ldap_options['optReferrals'])) $config->ldap->options->optReferrals = filter_var($ldap_options['optReferrals'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		$config->save();
	}

	static public function setAuthAdapter($adapterMethod)
	{
		$config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
		$config->authAdapterMethod = filter_var($adapterMethod, FILTER_SANITIZE_STRING);
		$config->save();
	}

	static public function setDefaultUser($defaultUser)
	{
		$config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
		$config->alwaysUseDatabaseForLogin = filter_var($defaultUser, FILTER_SANITIZE_STRING);
		$config->save();
	}
}
