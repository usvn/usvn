<?php
/**
 * Controller for check update
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

class CheckupdateController extends Zend_Controller_Action
{
	protected $_mimetype = 'image/png';

	public function indexAction()
	{
		USVN_Update::updateUSVNAvailableVersionNumber();
		$this->_helper->viewRenderer->setNoRender();
        header('Cache-Control: max-age=3600, must-revalidate');
        header("Content-type: $this->_mimetype");
        echo file_get_contents(USVN_PUB_DIR . "/medias/" . 'usvn' . "/images/empty.png");
	}
}
