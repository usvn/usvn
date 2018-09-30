<?php
/**
 * Check for update
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.7
 * @package usvn
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
class USVN_Update
{
	/**
	 * Run update
	 *
	 * @author Team USVN
	 * @return
	 */
	static public function runUpdate()
	{
		$config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
		if ($config->version == '1.0.0')
		{
			$config->version = '1.0.7';//DONT REPLACE WITH USVN_CONFIG_VERSION
			$config->save();
		}
		else if ($config->version == '1.0.1')//DONT REPLACE WITH USVN_CONFIG_VERSION
		{
			$config->version = '1.0.7';//DONT REPLACE WITH USVN_CONFIG_VERSION
			$config->save();
		}
		else if ($config->version == '1.0.2')//DONT REPLACE WITH USVN_CONFIG_VERSION
		{
			$config->version = '1.0.7';//DONT REPLACE WITH USVN_CONFIG_VERSION
			$config->save();
		}
        else if ($config->version == '1.0.3')//DONT REPLACE WITH USVN_CONFIG_VERSION
		{
			$config->version = '1.0.7';//DONT REPLACE WITH USVN_CONFIG_VERSION
			$config->save();
		}
        else if ($config->version == '1.0.4')//DONT REPLACE WITH USVN_CONFIG_VERSION
		{
			$config->version = '1.0.7';//DONT REPLACE WITH USVN_CONFIG_VERSION
			$config->save();
		}
        else if ($config->version == '1.0.5')//DONT REPLACE WITH USVN_CONFIG_VERSION
        {
            $config->version = '1.0.7';//DONT REPLACE WITH USVN_CONFIG_VERSION
            $config->save();
        }
        else if ($config->version == '1.0.6')//DONT REPLACE WITH USVN_CONFIG_VERSION
        {
            $config->version = '1.0.7';//DONT REPLACE WITH USVN_CONFIG_VERSION
            $config->save();
        }
        else if ($config->version == '1.0.7')//DONT REPLACE WITH USVN_CONFIG_VERSION
        {
            $config->version = '1.0.8';//DONT REPLACE WITH USVN_CONFIG_VERSION
            $config->save();
        }
        else
		{
			die("Cannot update from version {$config->version}");
//			$this->view->error = 'Cannot update from this version';
		}
	}
	/**
	 * @return bool True if we need to check update
	 */
	static public function itsCheckForUpdateTime()
	{
		$config = Zend_Registry::get('config');
		if (isset($config->update->lastcheckforupdate)) {
			if ($config->update->lastcheckforupdate > (time() - (60 * 60 * 24))) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Return available version on http://www.usvn.info
	 *
	 * @return string
	 */
	static public function getUSVNAvailableVersion()
	{
		$config = Zend_Registry::get('config');
		if (!isset($config->update->availableversion))
			return $config->version;
		return $config->update->availableversion;
	}

	/**
	 * Set proxy configuration information for check update.
	 * Get proxy from HTTP_HOST environnement variable.
	 *
	 * Only http://USER:PASSWORD@PROXY_SERVER:PORT is supported.
	 */
	static private function setProxyForUpdate($config)
	{
		$env = getenv("HTTP_PROXY");
		if ($env !== false)
		{
			$res = array();
			if (preg_match("#http://([^:]+):([^@]+)@([^:]+):([0-9]+)#", $env, $res))
			{
				$config['adapter'] = 'Zend_Http_Client_Adapter_Proxy';
				$config['proxy_host'] = $res[3];
				$config['proxy_port'] = $res[4];
				$config['proxy_user'] = $res[1];
				$config['proxy_pass'] = $res[2];
			}
		}
		return $config;
	}

	/**
	 * Update file with USVN version number
	 */
	static public function updateUSVNAvailableVersionNumber()
	{
		if (USVN_Update::itsCheckForUpdateTime())
		{
			$config = Zend_Registry::get('config');
			$url = 'http://www.usvn.info';
			$http_conf = USVN_Update::setProxyForUpdate(array('maxredirects' => 0, 'timeout' => 30));
			// if (defined("PHPUnit_MAIN_METHOD"))
			// 	$url = 'http://iceage.usvn.info';
			$client = new Zend_Http_Client($url . '/update/' . urlencode($config->version), $http_conf);
			$client->setParameterPost('sysinfo', USVN_Update::getInformationsAboutSystem());
			try
			{
				$response = $client->request('POST');
			}
			catch (Exception $e)
			{
				// Ugly but we don't want to display error if usvn.info is not available
				return;
			}
			if ($response->getStatus() == 200)
			{
				$config->update->availableversion = $response->getBody();
				$config->save();
			}
			$config->update->lastcheckforupdate = time();
			$config->save();
		}
	}

	/**
	 * Return informations about the system into a XML string.
	 *
	 * @return string XML
	 */
	static public function getInformationsAboutSystem()
	{
		$config = Zend_Registry::get('config');
		$xml = new SimpleXMLElement("<informations></informations>");
		$os = $xml->addChild('host');
		$os->addChild('os', PHP_OS);
		$os->addChild('uname', php_uname());
		$subversion = $xml->addChild('subversion');
		$subversion->addChild('version', implode(".", USVN_SVNUtils::getSvnVersion()));
		$usvn = $xml->addChild('usvn');
		$usvn->addChild('version', $config->version);
		$usvn->addChild('translation', $config->translation->locale);
		$usvn->addChild('databaseadapter', $config->database->adapterName);
		$php = $xml->addChild('php');
		$php->addChild('version', phpversion());
		// $ini = $php->addChild('ini');
		// foreach(ini_get_all() as $var => $value)
		// 	$ini->addChild($var, htmlspecialchars((string)$value['local_value']));
		// foreach (get_loaded_extensions() as $ext)
		// 	$php->addChild('extension', $ext);
		// $apache = $xml->addChild('host');
		// if (function_exists("apache_get_modules"))
		// 	foreach (apache_get_modules() as $ext)
		// 		$apache->addChild("module", $ext);
		return $xml->asXml();
	}
}
