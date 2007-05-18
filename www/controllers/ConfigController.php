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
 * $Id$
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'AdminController.php';

class ConfigController extends USVN_Controller
{

	public function indexAction()
	{
		$this->_view->config = Zend_Registry::get("config");
		$this->_render("index.html");
	}

	public function saveAction()
	{
		USVN_modules_admin_models_Config::setLanguage($_POST['language']);
		USVN_modules_admin_models_Config::setTemplate($_POST['template']);
		$siteDatas = array('title'			=> $_POST['siteTitle'],
							'ico'			=> $_POST['siteIco'],
							'logo'			=> $_POST['siteLogo']);
		USVN_modules_admin_models_Config::setSiteDatas($siteDatas);
		$this->_redirect('admin/config/');
	}
}
