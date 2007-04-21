<?php
/**
 * Manipulate the table rights
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package default
 * @subpackage models
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
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
	protected $_primary = "id";

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
	 * Associative array map of declarative referential integrity rules.
	 * This array has one entry per foreign key in the current table.
	 * Each key is a mnemonic name for one reference rule.
	 *
	 * Each value is also an associative array, with the following keys:
	 * - columns	= array of names of column(s) in the child table.
	 * - refTable   = class name of the parent table.
	 * - refColumns = array of names of column(s) in the parent table,
	 *				in the same order as those in the 'columns' entry.
	 * - onDelete   = "cascade" means that a delete in the parent table also
	 *				causes a delete of referencing rows in the child table.
	 * - onUpdate   = "cascade" means that an update of primary key values in
	 *				the parent table also causes an update of referencing
	 *				rows in the child table.
	 *
	 * @var array
	 */
	protected $_referenceMap = array(
	"ToAttribute" => array(
	"columns"	=> array("rights_id"),
	"refTable"   => "USVN_Db_Table_ToAttribute",
	"refColumns" => array("rights_id", "groups_id", "projects_id", "files_id"),
	)
	);

	/**
	 * Simple array of class names of tables that are "children" of the current
	 * table, in other words tables that contain a foreign key to this one.
	 * Array elements are not table names; they are class names of classes that
	 * extend Zend_Db_Table_Abstract.
	 *
	 * @var array
	 */
	protected $_dependentTables = array("USVN_Db_Table_ToAttribute");

    /**
	 * Check if the user is allwed to go to an action
	 *
	 * @param string $login The login name of the user
	 * @param string $project The project name, default if none
	 * @param string $module The module name
	 * @param string $controller The controller name
	 * @param string $action The action name
	 * @return bool true : the user is allwed, false : the user is not allwed
	 */
    public function access($login, $project, $module, $controller, $action)
    {
    	$acl = new Zend_Acl();
		$roleGuest = new Zend_Acl_Role('user');

		$right = $module . "_" . $controller . "_" . $action;
		$acl->addRole($roleGuest);
    	$db = $this->getAdapter();

		/**
	 	* Request for get all the right from the current user.
	 	*/
		
    	$select = $db->select();
    	$select->from(USVN_Db_Table::$prefix . 'to_attribute', '*');
    	$select->join(USVN_Db_Table::$prefix . 'projects', USVN_Db_Table::$prefix . 'to_attribute.projects_id = ' . USVN_Db_Table::$prefix . 'projects.projects_id');
    	$select->join(USVN_Db_Table::$prefix . 'users_to_groups', USVN_Db_Table::$prefix . 'to_attribute.groups_id = ' . USVN_Db_Table::$prefix . 'users_to_groups.groups_id');
    	$select->join(USVN_Db_Table::$prefix . 'rights', USVN_Db_Table::$prefix . 'to_attribute.rights_id = ' . USVN_Db_Table::$prefix . 'rights.rights_id');
    	$select->join(USVN_Db_Table::$prefix . 'users', USVN_Db_Table::$prefix . 'users.users_id = ' . USVN_Db_Table::$prefix . 'users_to_groups.users_id');
    	$select->where(USVN_Db_Table::$prefix . 'users.users_login = ?', $login);
    	$select->where(USVN_Db_Table::$prefix . 'projects.projects_name = ?', $project);

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

		try {
			if ($acl->isAllowed('user', $module . "_" . $controller, $action) == 1)
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
