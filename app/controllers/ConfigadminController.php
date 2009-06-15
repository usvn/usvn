<?php
/**
 * Controller for configuration pages
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
 * $Id: ConfigadminController.php 1306 2007-11-11 19:44:11Z duponc_j $
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'AdminadminController.php';

class ConfigadminController extends AdminadminController
{
	public function indexAction()
	{
		$this->view->config = Zend_Registry::get("config");
		if ($this->view->config->ldap == null)
		{
			$this->view->config->ldap = array();
			$this->view->config->ldap->options = array();
		}
        $this->view->locale = new Zend_Locale(USVN_Translation::getLanguage());
		$this->render("index");
	}

	public function saveAction()
	{
		USVN_Config::setLanguage($_POST['language']);
		USVN_Config::setTimeZone($_POST['timezone']);
		USVN_Config::setTemplate($_POST['template']);
		USVN_Config::setCheckForUpdate($_POST['checkforupdate']);
		$siteDatas = array('title'			=> $_POST['siteTitle'],
							'ico'			=> $_POST['siteIco'],
							'logo'			=> $_POST['siteLogo']);
		USVN_Config::setSiteDatas($siteDatas);
		USVN_Config::setDefaultUser($_POST['alwaysUseDatabaseForLogin']);
		USVN_Config::setAuthAdapter($_POST['authAdapterMethod']);
		$ldapEncryptMethod = $_POST['LDAPEncryptionMethod'];
		$_POST['ldap']['useStartTls'] = ($ldapEncryptMethod == 'tls' ? '1' : '0');
		$_POST['ldap']['useSsl'] = ($ldapEncryptMethod == 'ssl' ? '1' : '0');
		USVN_Config::setLDAPConfig($_POST['ldap']);
		$this->_redirect('/admin/config/');
	}
}
