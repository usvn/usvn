<?php
/**
 * Controller for display homepage of project page
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
class ProjectController extends USVN_Controller {
	public function indexAction() {
		$projects = new USVN_Db_Table_Projects;
		$where  = $projects->getAdapter()->quoteInto('projects_name = ?', $this->_request->getParam('project'));
		$this->_view->project = $projects->fetchRow($where);
		$this->_render();
	}
}
