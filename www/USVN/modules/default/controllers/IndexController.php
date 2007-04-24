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
	public function indexAction() {
		$projects = new USVN_Db_Table_Projects;
		if ($this->_view->project != '__NONE__') {
			$this->_view->project = $projects->fetchRow(array('projects_name = ?' => $this->_request->getParam('project')));
			$this->_render("project.html");
		}
		else {
			$this->_view->projects = $projects->fetchAll(array('projects_name != ?' => '__NONE__'));
			$this->_render("index.html");
		}
	}
}
