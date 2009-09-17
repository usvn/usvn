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
			$config->translation->locale = $language;
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
			$config->timezone = $timezone;
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
			$config->template->name = $template;
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
		$config->update = array("checkforupdate" => $check, "lastcheckforupdate" => 0);
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
		$config->site->title = $datas['title'];
		$config->site->ico = $datas['ico'];
		$config->site->logo = $datas['logo'];
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
		$config->ldap->createGroupForUserInDB = $ldap_options['createGroupForUserInDB'];
		$config->ldap->createUserInDBOnLogin = $ldap_options['createUserInDBOnLogin'];
		if (strlen($ldap_options['host'])) $config->ldap->options->host = $ldap_options['host'];
		if (strlen($ldap_options['port'])) $config->ldap->options->port = $ldap_options['port'];
		if (strlen($ldap_options['username'])) $config->ldap->options->username = $ldap_options['username'];
		if (strlen($ldap_options['password'])) $config->ldap->options->password = $ldap_options['password'];
		if (strlen($ldap_options['useStartTls'])) $config->ldap->options->useStartTls = $ldap_options['useStartTls'];
		if (strlen($ldap_options['useSsl'])) $config->ldap->options->useSsl = $ldap_options['useSsl'];
		if (strlen($ldap_options['bindDnFormat'])) $config->ldap->options->bindDnFormat = $ldap_options['bindDnFormat'];
		if (strlen($ldap_options['bindRequiresDn'])) $config->ldap->options->bindRequiresDn = $ldap_options['bindRequiresDn'];
		if (strlen($ldap_options['baseDn'])) $config->ldap->options->baseDn = $ldap_options['baseDn'];
		if (strlen($ldap_options['accountCanonicalForm'])) $config->ldap->options->accountCanonicalForm = $ldap_options['accountCanonicalForm'];
		if (strlen($ldap_options['accountDomainName'])) $config->ldap->options->accountDomainName = $ldap_options['accountDomainName'];
		if (strlen($ldap_options['accountDomainNameShort'])) $config->ldap->options->accountDomainNameShort = $ldap_options['accountDomainNameShort'];
		if (strlen($ldap_options['accountFilterFormat'])) $config->ldap->options->accountFilterFormat = $ldap_options['accountFilterFormat'];
		if (strlen($ldap_options['allowEmptyPassword'])) $config->ldap->options->allowEmptyPassword = $ldap_options['allowEmptyPassword'];
		if (strlen($ldap_options['optReferrals'])) $config->ldap->options->optReferrals = $ldap_options['optReferrals'];
		$config->save();
	}

	static public function setAuthAdapter($adapterMethod)
	{
		$config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
		$config->authAdapterMethod = $adapterMethod;
		$config->save();
	}

	static public function setDefaultUser($defaultUser)
	{
		$config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
		$config->alwaysUseDatabaseForLogin = $defaultUser;
		$config->save();
	}
}
