<?php
/**
 * Controller for login into default module
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
require_once 'USVN/modules/default/controllers/IndexController.php';

class CssController extends IndexController
{
	protected $_mimetype = 'text/css';

	public function screenAction()
	{
		$this->_view->css_file = USVN_MEDIAS_DIRECTORY . '/' . USVN_Template::getTemplate() . '/screen.css';
		$this->_view->image_directory = $this->_request->getBaseUrl() .'/medias/' . USVN_Template::getTemplate() . '/images';
		$this->_render('screen.css');
	}

	public function printAction()
	{
		$this->_view->css_file = USVN_MEDIAS_DIRECTORY . '/' . USVN_Template::getTemplate() . '/print.css';
		$this->_view->image_directory = $this->_request->getBaseUrl() .'/medias/' . USVN_Template::getTemplate() . '/images';
		$this->_render('print.css');
	}
}
