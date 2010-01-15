<?php
/**
 * Hooks management
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package admin
 * @subpackage group
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This group has been realised as part of
 * end of studies group.
 *
 * $Id$
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'AdminadminController.php';

class HooksadminController extends AdminadminController
{
	public function indexAction()
	{
		$table = new USVN_Db_Table_Hooks();
		$this->view->hooks = $table->fetchAll();
		foreach($this->view->hooks as $hook)
		{
			echo '<pre>';
			$projects = $hook->findManyToManyRowset('USVN_Db_Table_Projects', 'USVN_Db_Table_ProjectsToHooks');
			foreach ($projects as $proj)
			{
				echo $proj->name . "\n";
			}
			echo '</pre>';
		}
	}

	public function editAction()
	{
		
	}

	public function newAction()
	{
		
	}
}

?>