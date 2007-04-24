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

class changeset_IndexController extends USVN_Controller
{
	public function indexAction()
    {
		if ($this->_view->project ===  '__NONE__') {
			throw new USVN_Exception(T_("Invalid project."));
		}
		if (isset($_POST['revision'])) {
			$revision = $_POST['revision'];
		}
		else {
			$revision = $this->getRequest()->getParam('revision');
		}
		$projects = new USVN_Db_Table_Projects();
		$project = $projects->fetchRow(array("projects_name = ?" => $this->_view->project));
		if ($project ===  false) {
			throw new USVN_Exception(T_("Invalid project."));
		}
		$this->_view->revision = new USVN_Versioning_Revision($project->id, $revision);
		$this->_render('index.html');
    }
}
