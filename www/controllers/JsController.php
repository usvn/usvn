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

class JsController extends Zend_Controller_Action
{
	protected $_mimetype = 'text/javascript';

	public function indexAction()
	{
		//directories for medias and images
		$this->medias_directory = $this->_request->getBaseUrl() .'/medias';
		$this->images_directory = $this->_request->getBaseUrl() .'/medias/' . USVN_Template::getTemplate() . '/images';

		//directories for javascript
		$this->js_default_directory = $this->_request->getBaseUrl() . '/medias/default/js';
		$this->js_directory = $this->_request->getBaseUrl() . '/medias/' . USVN_Template::getTemplate() . '/js';

		//javascript files to load: the firts required and the second for templates
		header("Content-type: $_mimetype");
		include(USVN_MEDIAS_DIRECTORY . '/default/js/usvn.js');
		include(USVN_MEDIAS_DIRECTORY . '/' . USVN_Template::getTemplate() . '/script.js');
	}
}
