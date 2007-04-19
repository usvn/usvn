<?php
/**
 * Controller for js into default module
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
 * $Id: JsController.php 323 2007-04-16 15:12:49Z dolean_j $
 */
require_once 'USVN/modules/default/controllers/IndexController.php';

class JsController extends IndexController
{
	protected $_mimetype = 'text/javascript';

	public function indexAction()
	{
		//directories for medias and images
		$this->_view->medias_directory = $this->_request->getBaseUrl() .'/medias';
		$this->_view->images_directory = $this->_request->getBaseUrl() .'/medias/' . USVN_Template::getTemplate() . '/images';

		//directories for javascript
		$this->_view->js_default_directory = $this->_request->getBaseUrl() . '/medias/default/js';
		$this->_view->js_directory = $this->_request->getBaseUrl() . '/medias/' . USVN_Template::getTemplate() . '/js';

		//javascript files to load: the firts required and the second for templates
		$this->_view->js_default_file = USVN_MEDIAS_DIRECTORY . '/default/js/usvn.js';
		$this->_view->js_file = USVN_MEDIAS_DIRECTORY . '/' . USVN_Template::getTemplate() . '/script.js';
		$this->_render('script.js');
	}
}
