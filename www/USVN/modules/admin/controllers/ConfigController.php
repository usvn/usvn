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

require_once 'USVN/modules/admin/controllers/IndexController.php';


class admin_ConfigController extends admin_IndexController
{
	public function saveAction()
	{
		//fopen et close et serie.... c'est mal !
		USVN_modules_admin_models_Config::setLanguage($_POST['language']);
		USVN_modules_admin_models_Config::setTemplate($_POST['template']);
		$urlDatas = array('title' 			=> $_POST['urlTitle'],
							'description' 	=> $_POST['urlDescription'],
							'keywords' 		=> $_POST['urlKeywords']);
		USVN_modules_admin_models_Config::setUrlDatas($urlDatas);
		$siteDatas = array('name'			=> $_POST['siteName'],
							'ico'			=> $_POST['siteIco'],
							'description'	=> $_POST['siteDescription'],
							'logo'			=> $_POST['siteLogo']);
		USVN_modules_admin_models_Config::setSiteDatas($siteDatas);
		$this->_redirect('admin/config/');
	}
}
