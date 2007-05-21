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

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'AdminadminController.php';

class ConfigadminController extends AdminadminController
{
	public function indexAction()
	{
		$this->_view->config = Zend_Registry::get("config");
        $this->_view->locale = new Zend_Locale(USVN_Translation::getLanguage());
		$this->_render("index.html");
	}

	public function saveAction()
	{
		USVN_Config::setLanguage($_POST['language']);
		USVN_Config::setTemplate($_POST['template']);
		$siteDatas = array('title'			=> $_POST['siteTitle'],
							'ico'			=> $_POST['siteIco'],
							'logo'			=> $_POST['siteLogo']);
		USVN_Config::setSiteDatas($siteDatas);
		$this->_redirect('admin/config/');
	}
}
