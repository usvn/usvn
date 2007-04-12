<?php
/**
 * Main controller of the changeset module
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package changeset
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

class changeset_IndexController extends IndexController
{
	public function indexAction()
    {
		if ($this->_view->project ===  __NONE__) {
			throw new USVN_Exception(T_("Invalid project."));
		}
		$this->_view->assign('revision', $this->getRequest()->getParam('revision'));
		$this->_render('index.html');
    }
}
