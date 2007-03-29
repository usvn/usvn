<?php
/**
 * Model for files table
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info/
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
 * $Id $
 */

/**
 * Model for files table
 * 
 * Extends USVN_Db_Table for magic configuration and methods
 *
 */
class USVN_Db_Table_Files extends USVN_Db_Table {
	/**
	 * The primary key column (underscore format).
	 *
	 * Without our prefixe...
	 * 
	 * @var array
	 */
	protected $_primary = array("projects_id", "revisions_num","files_id");

	/**
	 * The table name derived from the class name (underscore format).
	 *
	 * @var array
	 */
	protected $_name = "files";

	/**
	 * Function which return the last files's version
	 *
	 * @param int $files_id
	 * @param int $version
	 * @return USVN_Db_Table_Row
	 */
	public function fetchMaxVersionFiles($files_id, $version = null)
	{
		$db = $this->getAdapter();
		/* @var $db Zend_Db_Adapter_Abstract */

		$where = $db->quoteInto('files_path = ?', $this->path);
		if ($version != null) {
			$where .= $db->quoteInto(' AND revisions_num <= ?', $version);
		}

		Zend_Debug::dump($this->_cols);
		$cols = $this->_cols;
		$this->_cols = array('max(revisions_num) as revisions_num');
		$ret = $this->fetchRow($where, null, 'files_path');
		$this->_cols = $cols;
		return $ret;
	}

	/**
	 * Search all matching files
	 *
	 * Return a Zend_Db_Table_Rowset containing all matching files for $project_id and $revisions
	 * where revisions is the actual max value
	 * 
	 * @param int $project_id
	 * @param int $revisions
	 * @return Zend_Db_Table_Rowset
	 */
	public function fetchGetFiles($project_id, $revisions)
	{
		$db = $this->getAdapter();
		/* @var $db Zend_Db_Adapter_Abstract */

		$where = $db->quoteInto('projects_id = ?', $project_id);
		$where .= $db->quoteInto(' AND revisions_num = ?', $revisions);

		return $this->fetchAll($where);
	}

	/**
	 * 
	 * @todo Faire le tag PHPdoc
	 * @todo Renommer la methode; elle doit permettre de trouver un rowset de fichier facilement
	 * @param unknown_type $project
	 * @param unknown_type $path
	 * @param unknown_type $version
	 */
	static public function getInstance($project, $path, $version)
	{
		$this->path = $path;
		$this->project = $project;
		//On recherche le fichier dans la DB et on met $this->version � la version trouv�e (Voir getVersion pour plus d'explication)
		//on recherche l'id du fichier en private
		$files = new USVN_Db_Table_Files();

		$files = $table->fetchRow('files_path =' . $this->path);
		$this->files_id = $files->files_id;

		//$this->version = getversion();
		$table = new USVN_Db_Table_Files();

		$result = $table->fetchMaxVersionFiles($this->files_id);

		$this->version = $result->revisions_num;
	}
}
