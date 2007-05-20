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
 * $Id$
 */

class CssController extends Zend_Controller_Action
{
	protected $_mimetype = 'text/css';

	public function screenAction()
	{
		$this->medias_directory = $this->_request->getBaseUrl() .'/medias';
		$this->images_directory = $this->_request->getBaseUrl() .'/medias/' . USVN_Template::getTemplate() . '/images';
		$this->css_directory = USVN_MEDIAS_DIRECTORY . '/' . USVN_Template::getTemplate() . '/css';
		$this->css_file = USVN_MEDIAS_DIRECTORY . '/' . USVN_Template::getTemplate() . '/screen.css';
        header('Cache-Control: max-age=3600, must-revalidate');
		header("Content-type: $_mimetype");
		include(USVN_MEDIAS_DIRECTORY . '/' . USVN_Template::getTemplate() . '/screen.css');
	}

	public function printAction()
	{
		$this->medias_directory = $this->_request->getBaseUrl() .'/medias';
		$this->images_directory = $this->_request->getBaseUrl() .'/medias/' . USVN_Template::getTemplate() . '/images';
		$this->css_directory = USVN_MEDIAS_DIRECTORY . '/' . USVN_Template::getTemplate() . '/css';
		$this->css_file = USVN_MEDIAS_DIRECTORY . '/' . USVN_Template::getTemplate() . '/print.css';
        header('Cache-Control: max-age=3600, must-revalidate');
		header("Content-type: $_mimetype");
		include(USVN_MEDIAS_DIRECTORY . '/' . USVN_Template::getTemplate() . '/print.css');
	}
}
