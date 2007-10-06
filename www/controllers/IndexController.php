<?php
/**
 * Main controller
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
class IndexController extends USVN_Controller {
	/**
	 * Default action for every controller.
	 *
	 */
	public function indexAction() {
		$projects = new USVN_Db_Table_Projects();
		$this->view->projects = $projects->fetchAllAssignedTo($this->getRequest()->getParam('user'));

		$identity = Zend_Auth::getInstance()->getIdentity();
		$user_table = new USVN_Db_Table_Users();
		$user = $user_table->fetchRow(array('users_login = ?' => $identity['username']));
		$this->view->groups = $user->listGroups();
	}
}
