<?php
/**
 * Controller for css into default module
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package default
 * @subpackage controller
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id: CssController.php 1188 2007-10-06 12:03:17Z crivis_s $
 */

class CssController extends Zend_Controller_Action
{
	protected $_mimetype = 'text/css';

	public function preDispatch()
	{
		$this->_helper->viewRenderer->setNoRender();
	}

	public function screenAction()
	{
		$this->css_directory = $this->_request->getBaseUrl().'/medias/usvn/stylesheets';
		$this->images_directory = $this->_request->getBaseUrl().'/medias/usvn/images';
		header('Cache-Control: max-age=3600, must-revalidate');
		header("Content-type: $this->_mimetype");
		include(USVN_MEDIAS_DIR.'/' . USVN_Template::getTemplate() . '/stylesheets/new.css');
		include(USVN_MEDIAS_DIR.'/' . USVN_Template::getTemplate() . '/stylesheets/bubblerights.css');
		include(USVN_MEDIAS_DIR.'/' . USVN_Template::getTemplate() . '/stylesheets/completion.css');
		include(USVN_MEDIAS_DIR.'/' . USVN_Template::getTemplate() . '/stylesheets/timeline.css');
		include(USVN_MEDIAS_DIR.'/' . USVN_Template::getTemplate() . '/stylesheets/datepicker.css');
	}

	public function printAction()
	{
		$this->css_directory = $this->_request->getBaseUrl().'/medias/usvn/stylesheets';
		$this->images_directory = $this->_request->getBaseUrl().'/medias/usvn/images';
		header('Cache-Control: max-age=3600, must-revalidate');
		header("Content-type: $this->_mimetype");
		include(USVN_MEDIAS_DIR.'/' . USVN_Template::getTemplate() . '/stylesheets/new.css');
		include(USVN_MEDIAS_DIR.'/' . USVN_Template::getTemplate() . '/stylesheets/bubblerights.css');
		include(USVN_MEDIAS_DIR.'/' . USVN_Template::getTemplate() . '/stylesheets/completion.css');
		include(USVN_MEDIAS_DIR.'/' . USVN_Template::getTemplate() . '/stylesheets/timeline.css');
		include(USVN_MEDIAS_DIR.'/' . USVN_Template::getTemplate() . '/stylesheets/datepicker.css');
	}
}
