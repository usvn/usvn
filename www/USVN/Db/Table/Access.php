<?php
/**
 * Model for users table
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package USVN_Db
 * @subpackage Table
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id: Users.php 338 2007-04-19 16:18:35Z attal_m $
 */


require_once 'Zend/Acl.php';
require_once 'Zend/Acl/Role.php';

class USVN_Db_Table_Access extends USVN_Db_Table  {
	/**
	 * The primary key column (underscore format).
	 *
	 * Without our prefixe...
	 *
	 * @var string
	 */
	protected $_primary = "rights_id";

	/**
	 * The field's prefix for this table.
	 *
	 * @var string
	 */
	protected $_fieldPrefix = "rights_";

	/**
	 * The table name derived from the class name (underscore format).
	 *
	 * @var array
	 */
    protected $_name = "rights";

	/**
	 * Simple array of class names of tables that are "children" of the current
	 * table, in other words tables that contain a foreign key to this one.
	 * Array elements are not table names; they are class names of classes that
	 * extend Zend_Db_Table_Abstract.
	 *
	 * @var array
	 */
	protected $_dependentTables = array("USVN_Db_Table_Workgroups");


	/**
	 * Add rights in the acl class
	 *
	 * @param string $action The action name
	 * @param string $login The login name of the user
	 * @param string $project The project name
	 */

	private function projectAccess(&$acl, $login, $project)
	{
		/**
	 	* Request for get all the right from the current user.
	 	*/

		$db = $this->getAdapter();
    	$select = $db->select();
    	$select->from(USVN_Db_Table::$prefix . 'workgroups_to_rights', '*');
    	$select->join(USVN_Db_Table::$prefix . 'workgroups', USVN_Db_Table::$prefix . 'workgroups.workgroups_id = ' . USVN_Db_Table::$prefix . 'workgroups_to_rights.workgroups_id');
    	$select->join(USVN_Db_Table::$prefix . 'projects', USVN_Db_Table::$prefix . 'workgroups.projects_id = ' . USVN_Db_Table::$prefix . 'projects.projects_id');
    	$select->join(USVN_Db_Table::$prefix . 'users_to_groups', USVN_Db_Table::$prefix . 'workgroups.groups_id = ' . USVN_Db_Table::$prefix . 'users_to_groups.groups_id');
    	$select->join(USVN_Db_Table::$prefix . 'rights', USVN_Db_Table::$prefix . 'workgroups_to_rights.rights_id = ' . USVN_Db_Table::$prefix . 'rights.rights_id');
    	$select->join(USVN_Db_Table::$prefix . 'users', USVN_Db_Table::$prefix . 'users.users_id = ' . USVN_Db_Table::$prefix . 'users_to_groups.users_id');
    	$select->where(USVN_Db_Table::$prefix . 'users.users_login = ?', $login);
    	$select->where(USVN_Db_Table::$prefix . 'projects.projects_name = ?', $project);
		$select->orWhere(USVN_Db_Table::$prefix . 'workgroups.groups_id = 1');
		if ($login != "anonymous") {
			$select->orWhere(USVN_Db_Table::$prefix . 'workgroups.groups_id = 2');
		}
    	$result = $db->fetchAll($select);
    	for ($i = 0; $i < count($result); $i++) {
			$tab = split('_', $result[$i]['rights_label']);
			try {
				$acl->add(new Zend_Acl_Resource($tab[0] . "_" . $tab[1]));
			} catch (Exception  $e) {

			}

			if (count($tab) == 3) {
				if ($result[$i]['is_right'] == true) {
					$acl->allow('user', $tab[0] . "_" . $tab[1], $tab[2]);
				} else {
					$acl->deny('user', $tab[0] . "_" . $tab[1], $tab[2]);
				}
			} elseif (count($tab) == 2) {
				if ($result[$i]['is_right'] == true) {
					$acl->allow('user', $tab[0] . "_" . $tab[1]);
				} else {
					$acl->deny('user', $tab[0] . "_" . $tab[1]);
				}
			} else {
				// Error in the right label
			}
		}
	}

	/**
	 * Check if the user is allwed to go to an action
	 *
	 * @param string $login The login name of the user
	 * @param string $project The project name, default if none
	 * @param string $controller The controller name
	 * @param string $action The action name
	 * @return bool true : the user is allwed, false : the user is not allowed
	 */
    public function access($login, $project, $controller, $action)
    {
    	$acl = new Zend_Acl();
		$roleGuest = new Zend_Acl_Role('user');

		$right = $controller . "_" . $action;
		$acl->addRole($roleGuest);
		if ($project != "__NONE__") {
			$this->projectAccess($acl, $login, "__NONE__");
		}
		$this->projectAccess($acl, $login, $project);

		try {
			if ($acl->isAllowed('user', $controller, $action) == 1)
			{
				//echo "<br>" . __FILE__ . ":" . "<b>L'utilisateur $login dispose du droit $right</b><br>";
    			return true;
    		} else {
    			//echo "<br>" . __FILE__ . ":" . "<b>L'utilisateur $login ne dispose pas du droit $right</b><br>";
    			return false;
    		}

		} catch (Exception  $e) {
			//echo "<br>" . __FILE__ . ":" . "<b>L'utilisateur $login ne dispose pas du droit $right</b><br>";
			return false;
   		}
    }
}
